<?php
/**
 * Workflow Exchange
 * @author 陶之11
 * @link http://www.pao11.com
 * @see https://github.com/dcto/workflows_exchange
 * @version 20161020
 */
error_reporting(0);


/**
 * Class Exchange
 */
class Exchange{

    /**
     * [$api]
     * @var string
     */
    private $api = 'http://api.fixer.io/latest';

    /**
     * @var string
     */
    private $base = 'CNY';


    /**
     * @var int
     */
    private $amount = 1;

    /**
     * [$currency]
     * @var array
     */
    private $currency = array(
        'USD' => array('name' => '美元',  'flag' => 'USD'),
        'CNY' => array('name' => '人民币', 'flag' => 'CNY'),
        'HKD' => array('name' => '港币',  'flag' => 'HKD'),
        'JPY' => array('name' => '日元',  'flag' => 'JPY'),
        'KRW' => array('name' => '韩元',  'flag' => 'KRW'),
        //'TWD' => array('name' => '新台币', 'flag' => 'TWD'),
        'EUR' => array('name' => '欧元',  'flag' => 'EUR'),
        'GBP' => array('name' => '英镑',  'flag' => 'GBP'),
        'AUD' => array('name' => '澳元',  'flag' => 'AUD'),
        'THB' => array('name' => '泰铢',  'flag' => 'THB'),
        'MYR' => array('name' => '马来西亚令吉特', 'flag' => 'MYR'),
        'PHP' => array('name' => '菲律宾比索',   'flag' => 'PHP'),
        'SGD' => array('name' => '新加坡元',    'flag' => 'SGD'),
        'CAD' => array('name' => '加拿大元',    'flag' => 'CAD'),
        'CHF' => array('name' => '瑞士法郎',    'flag' => 'CHF'),
        'IDR' => array('name' => '印尼盾', 'flag' => 'IDR'),
        'NZD' => array('name' => '新西兰元',  'flag' => 'NZD'),
        'SEK' => array('name' => '瑞典克朗', 'flag' => 'SEK'),
        //'VND' => array('name' => '越南盾', 'flag' => 'VND'),
        'ZAR' => array('name' => '南非兰特', 'flag' => 'ZAR')
    );

    public function __construct($args = '1CNY')
    {
        if(is_numeric($args)){
            $this->amount = $args;
        }else{
            $args = strtoupper($args);
            $this->amount = str_replace(array_keys($this->currency), '', $args);
            $this->base = trim(str_replace($this->amount, '', $args));
        }
    }

    /**
     * getCurrency
     */
    private function getCurrency()
    {
        if(filemtime('api_cache.'.$this->base)+86400 > time()){
            $json = file_get_contents('api_cache.'.$this->base);
        }else{
            $json = @file_get_contents($this->api = $this->api.'?base='.$this->base.'&symbols='.implode(',', array_keys($this->currency)));
            file_put_contents('api_cache.'.$this->base, $json);
        }
        return json_decode($json, true);
    }


    /**
     * converter result
     */
    private function getConverter()
    {
        $i = 0;

        $array = array();

        $currency = $this->getCurrency();

        foreach ($this->currency as $k => $v){
            if($k == $this->base) continue;
            $array[] = array(
                'uid'      => $i ++,
                'arg'      => $this->amount * $currency['rates'][$k],
                'title'    => $k.': '.$this->amount * $currency['rates'][$k] . $v['name'],
                'subtitle' => '当前输入货币:'.$this->currency[$this->base]['name'].', 汇率更新时间:' . $currency['date'],
                'icon'     => 'flags/' . $k . '.png'
            );
        }
        return $array;
    }

    /**
     * display result
     */
    public function output()
    {
        require( 'workflows.php' );
        $workflow = new Workflows();

       echo $workflow->toxml( $this->getConverter() );

    }

}


