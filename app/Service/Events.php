<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */

namespace App\Service;
use Exception;
use HP\Swoole\Api;
use HP\Component\Singleton;

/**
 * 事件管理
 */
class Events {

    use Singleton;

    /**
     * 当客户端断开连接时
     * @param $client_id
     */
    public function onClose($client_id){
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";

        // 从房间的客户端列表中删除
        if(isset($_SESSION['room_id'])) {
            $room_id = $_SESSION['room_id'];
            $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
            Api::sendToGroup($room_id, json_encode($new_message));
        }
    }

    /**
     * @param $client_id
     * @param $message
     * @return bool|void
     * @throws Exception
     */
    public function onMessage($client_id, $message){
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";

        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data) {
            return ;
        }

        // 根据类型执行不同的业务
        switch($message_data['type']) {
            // 客户端回应服务端的心跳
            case 'ping':
                Api::sendToClient($client_id, json_encode([
                    'type'=>'pong'
                ]));
                break;
            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
                // 判断是否有房间号
                if(!isset($message_data['room_id'])) {
                    throw new Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }

                // 把房间号昵称放到session中
                $room_id = $message_data['room_id'];
                $client_name = htmlspecialchars($message_data['client_name']);
                $_SESSION['room_id'] = $room_id;
                $_SESSION['client_name'] = $client_name;

                // 获取房间内所有用户列表
                $clients_list = Api::getClientListByGroup($room_id);
                foreach($clients_list as $tmp_client_id=>$item) {
                    $clients_list[$tmp_client_id] = $item['client_name'];
                }
                $clients_list[$client_id] = $client_name;

                // 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx}
                $new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'client_name'=>htmlspecialchars($client_name), 'time'=>date('Y-m-d H:i:s'));
                Api::sendToGroup($room_id, json_encode($new_message));
                Api::joinGroup($client_id, $room_id);

                // 给当前用户发送用户列表
                $new_message['client_list'] = $clients_list;
                Api::sendToClient($client_id, json_encode($new_message));
                return;

            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    throw new Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                }
                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];

                if($message_data['to_client_id'] != 'all')
                {
                    // 私聊
                    $new_message = array(
                        'type'=>'say',
                        'from_client_id'=>$client_id,
                        'from_client_name' =>$client_name,
                        'to_client_id'=>$message_data['to_client_id'],
                        'content'=>"<b>对你说: </b>".nl2br(htmlspecialchars($message_data['content'])),
                        'time'=>date('Y-m-d H:i:s'),
                    );
                    Api::sendToClient($message_data['to_client_id'], json_encode($new_message));
                    $new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
                    return Api::$client_id($client_id, json_encode($new_message));
                }else{
                    //群发消息
                    $new_message = array(
                        'type'=>'say',
                        'from_client_id'=>$client_id,
                        'from_client_name' =>$client_name,
                        'to_client_id'=>'all',
                        'content'=>nl2br(htmlspecialchars($message_data['content'])),
                        'time'=>date('Y-m-d H:i:s'),
                    );
                    Api::sendToGroup($room_id ,json_encode($new_message));
                }
        }
    }
}