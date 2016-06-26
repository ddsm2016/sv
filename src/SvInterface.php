<?php
namespace Sv;

use Ddsm\Szrk\Sms;
use Illuminate\Database\Eloquent\Model;

interface SvInterface
{
    /**
     * 发送短信验证码
     *
     * @param string $phone 接收验证码手机号
     *
     * @return bool
     */
    function send($phone);

    /**
     * 验证码验证
     *
     * @param string $inputCode 待验证的输入验证码
     *
     * @return bool
     */
    function verify($inputCode);

    /**
     * 获取最后产生错误信息
     *
     * @return string
     */
    function error();

    /**
     * 产生一个当前实现类的实例
     *
     * @param string $id 实例ID
     *
     * @return $this
     */
    static function instance($id);

    /**
     * 设置配置文件
     *
     * @param array $config
     *
     * @return $this
     */
    function config($config);

    /**
     * 设置当前实例模型
     *
     * @param Model $model
     *
     * @return $this
     */
    function model($model);

    /**
     * 设置短信发送类实例
     *
     * @param Sms $sms
     *
     * @return $this
     */
    function sms($sms);

    function content($template, $length = 4, $string = '');
}