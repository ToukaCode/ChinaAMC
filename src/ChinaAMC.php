<?php
namespace ToukaCode;

use Exception;
use ToukaCode\Services\ServicesJson;

/**
 * 华夏基金净值提取
 */
class ChinaAMC {
	/**
	 * 提取地址模板
	 *
	 * @var string
	 */
	public static $UriTemplate = "https://m.chinaamc.com/fund/%s/JzzsData.js";

	/**
	 * 提取
	 *
	 * @param string $code 基金代码
	 * @return array
	 */
	public static function get(string $code) {
		$url = sprintf(static::$UriTemplate, $code);
		if(!$data = file_get_contents($url)) {
			throw new Exception("下载数据出错");
		}
		if(!$content = preg_replace("/^var JzzsDataObj=(.+);$/", "$1", $data)) {
			throw new Exception("下载数据不完整");
		}
		$json = new ServicesJson();
		$data = $json->decode($content);
		if(!isset($data->oneMonthNavList) || !isset($data->threeMonthNavList) || !isset($data->oneYearNavList)) {
			throw new Exception("数据格式有误");
		}
		$oneMonth   = array_combine($data->oneMonthNavList->time, $data->oneMonthNavList->value);
		$threeMonth = array_combine($data->threeMonthNavList->time, $data->threeMonthNavList->value);
		$oneYear    = array_combine($data->oneYearNavList->time, $data->oneYearNavList->value);
		return compact("oneMonth", "threeMonth", "oneYear");
	}
}