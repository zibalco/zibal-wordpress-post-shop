<?php

class ps_zibal extends ps_payment_gateway
{
    public $merchant;
    private $trackId;

    //	public $testMode = false;

    public function __construct()
    {
        //	self::load_nusoap();
    }

    public function send($callback, $price, $username, $email, $order_id)
    {
        $data = array(
          "merchant" => $this->merchant,
          "amount" => $price,
          "description" => "خرید از سایت با استفاده از افزونه فروش پست",
          "callbackUrl" => $callback
		);

        $result = postToZibal("request", $data);
        $result = (array)$result;

        if ($result['result'] == 100) {
            $this->trackId = $result['result'];
            $this->insert_payment($username, $price, $order_id, $email);
            echo $this->info_alert('در حال اتصال به درگاه ...');
            $url = 'https://gateway.zibal.ir/start/'.$result['trackId'];
            $this->redirect($url);
        } else {
            echo $this->danger_alert('خطا در متصل شدن به درگاه ! :'.$result['result']);
        }
    }

    public function verify($price, $post_id, $order_id, $course_id = 0)
    {
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 2) {
                $data = array(
                    "merchant" => $this->merchant,
                    "trackId" => $this->trackId
                );

                $result = $this->postToZibal("verify", $data);
                $result = (array)$result;

                if ($result['result'] == 100 && $price == $result["amount"]) {
                    $this->success_payment($result['trackId'], $order_id, $price, $post_id, $course_id);
                } else {
                    echo $this->danger_alert('خطا در پردازش عملیات پرداخت ، نتیجه پرداخت : '.$result['result']);
                }
            } else {
                echo $this->danger_alert('پرداخت ناموفق!');
            }
            $this->end_payment();
        }
    }

    public function postToZibal($path, $parameters)
    {
        $url = 'https://gateway.zibal.ir/v1/'.$path;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}
