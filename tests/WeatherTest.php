<?php

/*
 * This file is part of the her-cat/weather.
 *
 * (c) her-cat <i@her-cat.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace HerCat\Weather\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use HerCat\Weather\Exceptions\HttpException;
use HerCat\Weather\Exceptions\InvalidArgumentException;
use HerCat\Weather\Weather;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;

/**
 * Class WeatherTest.
 */
class WeatherTest extends TestCase
{
    // 检查 $type 参数
    public function testGetWeatherWithInvalidType()
    {
        $weather = new Weather('mock-key');

        // 断言会抛出此异常类
        $this->expectException(InvalidArgumentException::class);

        // 断言异常消息为 'Invalid type value(base/all): foo'
        $this->expectExceptionMessage('Invalid type value(base/all): foo');

        $weather->getWeather('深圳', 'foo');

        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    public function testGetWeatherWithInvalidFormat()
    {
        $weather = new Weather('mock-key');

        // 断言会抛出此异常类
        $this->expectException(InvalidArgumentException::class);

        // 断言异常消息为 'Invalid response format(json/xml): array'
        $this->expectExceptionMessage('Invalid response format(json/xml): array');

        $weather->getWeather('深圳', 'base', 'array');

        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    public function testGetWeatherWithJson()
    {
        // 创建模拟接口响应值
        $response = new Response(200, [], '{"success": true}');

        // 创建模拟 http client
        $client = \Mockery::mock(Client::class);

        // 指定将会产生的行为（在后续的测试中将会按下面的参数来调用）。
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key' => 'mock-key',
                'city' => '深圳',
                'output' => 'json',
                'extensions' => 'base',
            ],
        ])->andReturn($response);

        // 将 `getHttpClient` 方法替换为上面创建的 http client 为返回值的模拟方法。
        $weather = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $weather->allows()->getHttpClient()->andReturn($client); // $client 为上面创建的模拟实例。

        $this->assertSame(['success' => true], $weather->getWeather('深圳'));
    }

    public function testGetWeatherWithXml()
    {
        // 创建模拟接口响应值
        $response = new Response(200, [], '<hello>content</hello>');

        // 创建模拟 http client
        $client = \Mockery::mock(Client::class);

        // 指定将会产生的行为（在后续的测试中将会按下面的参数来调用）。
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key' => 'mock-key',
                'city' => '深圳',
                'output' => 'xml',
                'extensions' => 'all',
            ],
        ])->andReturn($response);

        // 将 `getHttpClient` 方法替换为上面创建的 http client 为返回值的模拟方法。
        $weather = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $weather->allows()->getHttpClient()->andReturn($client); // $client 为上面创建的模拟实例。

        $this->assertSame('<hello>content</hello>', $weather->getWeather('深圳', 'all', 'xml'));
    }

    public function testGetWeatherWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs()) // 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $weather = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $weather->allows()->getHttpClient()->andReturn($client);

        // 断言调用时会产生异常。
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');

        $weather->getWeather('深圳');
    }

    public function testGetHttpClient()
    {
        $weather = new Weather('mock-key');

        // 断言返回结果为 GuzzleHttp\ClientInterface 实例
        $this->assertInstanceOf(ClientInterface::class, $weather->getHttpClient());
    }

    public function testSetGuzzleOptions()
    {
        $weather = new Weather('mock-key');

        // 设置参数前，timeout 为 null
        $this->assertNull($weather->getHttpClient()->getConfig('timeout'));

        // 设置参数
        $weather->setGuzzleOptions(['timeout' => 5000]);

        // 设置参数后，timeout 为 5000
        $this->assertSame(5000, $weather->getHttpClient()->getConfig('timeout'));
    }

    public function testGetLiveWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $weather = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $weather->expects()->getWeather('深圳', 'base', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $weather->getLiveWeather('深圳'));
    }

    public function testGetForecastsWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $weather = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $weather->expects()->getWeather('深圳', 'all', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $weather->getForecastsWeather('深圳'));
    }
}
