<?php
namespace Sv;

use Ddsm\Szrk\Sms;
use Illuminate\Database\Eloquent\Model;

class SmsVerify implements SvInterface
{
    private $id;

    private $config;

    /**
     * @var Sms
     */
    private $sms;

    /**
     * 记录最后产生的错误信息
     *
     * @var string
     */
    private $error;

    /**
     * @var Model
     */
    private $model;

    private $content;

    private $code;

    /**
     * 发送短信验证码
     *
     * @param string $phone 接收验证码手机号
     *
     * @return bool
     *
     * @throws \Exception
     */
    function send($phone)
    {
        if (is_null($this->sms)) {
            throw new \Exception('请先设置短信发送对象实例');
        }

        $recode = $this->model->find($this->id);
        if (!$recode) {
            $recode = $this->model;

            $recode->id     = $this->id;
            $recode->dayget = 0;
        } else {
            if ($recode->dayget >= $this->config->get_max) {
                $this->error = '当日获取次数超过限制次数';
                return false;
            }
        }

        $recode->code = $this->code;
        $recode->dayget += 1;
        $recode->verifyed = 0;

        $recode->save();

        $this->sms->sentOne($phone, $this->content);

        return true;
    }

    public function content($template, $length = 4, $string = '')
    {
        $string = $string ?: '0123456789';

        $this->code = $this->makeCode($length, $string);

        $this->content = sprintf($template, $this->code);

        return $this;
    }

    private function makeCode($length, $str)
    {
        $code = '';

        $strLength = strlen($str) - 1;

        for ($i = 0; $i < $length; $i++) {
            $code .= $str[ rand(0, $strLength) ];
        }

        return $code;
    }

    /**
     * 验证码验证
     *
     * @param string $inputCode 待验证的输入验证码
     *
     * @return bool
     */
    function verify($inputCode)
    {
        $recode = $this->model->find($this->id);

        if (!$recode) {
            $this->error = '请先获取短信验证码';
            return false;
        }

        // 验证码有效期过滤
        if ($recode->created_time->getTimestamp() + $this->config->expire_time < LARAVEL_START) {
            $this->error = '短信验证码已过期';
            return false;
        }

        // 已验证次数过滤
        if ($recode->verifyed >= $this->config->verify_max) {
            $this->error = '已验证次数超过限制';
            return false;
        }

        if ($recode->code === $inputCode) {
            $recode->delete();
            return true;
        } else {
            $recode->verifyed += 1;
            $recode->save();

            $this->error = '验证码输入错误';

            return false;
        }
    }

    /**
     * 获取最后产生错误信息
     *
     * @return string
     */
    function error()
    {
        return $this->error;
    }

    /**
     * 产生一个当前实现类的实例
     *
     * @param string $id 实例ID
     *
     * @return $this
     */
    static function instance($id)
    {
        return new static($id);
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * 设置配置文件
     *
     * @param array $config
     *
     * @return $this
     */
    function config($config)
    {
        $this->config = collect($config);
    }

    /**
     * 设置当前实例模型
     *
     * @param Model $model
     *
     * @return $this
     */
    function model($model)
    {
        $this->model = $model;
    }

    /**
     * 设置短信发送类实例
     *
     * @param Sms $sms
     *
     * @return $this
     */
    function sms($sms)
    {
        $this->sms = $sms;
    }

}