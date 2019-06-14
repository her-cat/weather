<h1 align="center"> Weather </h1>

<p align="center"> :rainbow: 基于高德开放平台的 PHP 天气信息组件。 </p>

[![Build Status](https://travis-ci.org/her-cat/weather.svg?branch=master)](https://travis-ci.org/her-cat/weather)

## 安装

```shell
$ composer require her-cat/weather -vvv
```

## 配置

在使用本扩展之前，你需要去 [高德开放平台](https://lbs.amap.com/dev/id/newuser) 注册账号，然后创建应用，获取应用的 API Key。

## 使用

```php
use HerCat\Weather\Weather;

$key = 'xxxxxxxxxxxxxxxxxxxxxx';

$weather = new Weather($key);
```

### 获取实时天气

```php
$response = $weather->getLiveWeather('深圳');
```

示例：

```json
{
    "status":"1",
    "count":"1",
    "info":"OK",
    "infocode":"10000",
    "lives":[
        {
            "province":"广东",
            "city":"深圳市",
            "adcode":"440300",
            "weather":"阴",
            "temperature":"24",
            "winddirection":"北",
            "windpower":"≤3",
            "humidity":"97",
            "reporttime":"2019-06-11 22:46:23"
        }
    ]
}
```

### 获取天气预报

```php
$response = $weather->getForecastsWeather('深圳', 'all');
```

示例：

```json
{
    "status":"1",
    "count":"1",
    "info":"OK",
    "infocode":"10000",
    "forecasts":[
        {
            "city":"深圳市",
            "adcode":"440300",
            "province":"广东",
            "reporttime":"2019-06-11 23:15:37",
            "casts":[
                {
                    "date":"2019-06-11",
                    "week":"2",
                    "dayweather":"阴",
                    "nightweather":"中雨",
                    "daytemp":"28",
                    "nighttemp":"25",
                    "daywind":"无风向",
                    "nightwind":"无风向",
                    "daypower":"≤3",
                    "nightpower":"≤3"
                },
                {
                    "date":"2019-06-12",
                    "week":"3",
                    "dayweather":"大雨",
                    "nightweather":"大暴雨",
                    "daytemp":"29",
                    "nighttemp":"26",
                    "daywind":"西南",
                    "nightwind":"西南",
                    "daypower":"4",
                    "nightpower":"4"
                },
                {
                    "date":"2019-06-13",
                    "week":"4",
                    "dayweather":"大暴雨",
                    "nightweather":"大雨",
                    "daytemp":"29",
                    "nighttemp":"25",
                    "daywind":"南",
                    "nightwind":"南",
                    "daypower":"4",
                    "nightpower":"4"
                },
                {
                    "date":"2019-06-14",
                    "week":"5",
                    "dayweather":"大雨",
                    "nightweather":"阵雨",
                    "daytemp":"30",
                    "nighttemp":"26",
                    "daywind":"无风向",
                    "nightwind":"无风向",
                    "daypower":"≤3",
                    "nightpower":"≤3"
                }
            ]
        }
    ]
}
```

### 获取 XML 格式返回值

第三个参数为返回值类型，可选 `json` 与 `xml`，默认 `json` ：

```php
$response = $weather->getLiveWeather('深圳', 'xml');
```

示例：

```xml
<?xml version="1.0" encoding="UTF-8"?><root>
  <status>1</status>
  <count>1</count>
  <info>OK</info>
  <infocode>10000</infocode>
  <lives type="list">
    <live>
      <province>广东</province>
      <city>深圳市</city>
      <adcode>440300</adcode>
      <weather>多云</weather>
      <temperature>30</temperature>
      <winddirection>东北</winddirection>
      <windpower>≤3</windpower>
      <humidity>57</humidity>
      <reporttime>2019-06-14 18:46:06</reporttime>
    </live>
  </lives>
</root>
```

### 参数说明

```php
array|string getLiveWeather(string $city, string $format = 'json')
array|string getForecastsWeather(string $city, string $format = 'json')
```

> - $city - 城市名，比如：“深圳”；
> - $format - 输出的数据格式，默认为 json 格式，当 output 设置为 “xml” 时，输出的为 XML 格式的数据。

### 在 Laravel 中使用

在 Laravel 中使用也是同样的安装方式，配置写在 `config/services.php` 中：

```php
.
.
.
'weather' => [
    'key' => env('WEATHER_API_KEY'),
],
```

然后在 `.env` 中配置 `WEATHER_API_KEY` ：

```dotenv
WEATHER_API_KEY=xxxxxxxxxxxxxxxxxxxxx
```

可以用两种方式来获取 `HerCat\Weather\Weather` 实例：

#### 方法参数注入

```php
.
.
.
public function show(Weather $weather) 
{
    $response = $weather->getLiveWeather('深圳');
}
.
.
.
```

#### 服务名访问

```php
.
.
.
public function show() 
{
    $response = app('weather')->getLiveWeather('深圳');
}
.
.
.
```

## 参考

- [高德开放平台](https://lbs.amap.com/dev/id/newuser)

## License

MIT