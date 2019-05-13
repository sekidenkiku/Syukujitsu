# Syukujitsu
日本の祝日を取得します。

## 特徴
- 国民の祝日に関する法律(1948年7月20日施行)で定められた国民の祝日と休日（振替休日、国民の休日）(以下、祝日とする)を取得します。
1948年7月20日以降の祝日が対象です。<br>
- 祝日は、DateTimeクラスの拡張クラスオブジェクトで返されます。<br>
- 春分の日、秋分の日は2150年まで対応しています。
- 2019年、2020年の祝日変更に対応済みです。<br>

## 注意事項
- 将来、法改正により、祝日の月日などが変更される場合があります。
- 春分の日と秋分の日は、法律で具体的に月日が明記されず、それぞれ「春分日」、「秋分日」と定められています。
本プログラムでは計算により求めた「春分日」、「秋分日」を使用しています。

## インストール要件
- PHP >= 7.1

## インストール方法

```php
$ composer require sekidenkiku/syukujitsu
```
使用例
```php
require_once('vendor/autoload.php');

use sekidenkiku\syukujitsu\Syukujitsu;

$syukujitsu = new Syukujitsu();
```
## 使用方法

**1.祝日のリストを取得**
```php
find(int $year, ?int $month = null): array
```
結果は、HolidayClassクラスのインスタントで返されます。HolidayClassクラスはDateTimeクラスの拡張クラスです。<br>
+ 月の祝日を取得する。<br>
```php
$syukujitsu = new Syukujitsu();

// 2020年5月を指定。
$holidays = $syukujitsu->find(2020, 5);

foreach($holidays as $holiday)
{
    // 返値$holidayはDateTimeクラスの拡張クラスオブジェクトなのでformat()で日付の書式を変更できます。
    echo $holiday->format("Y-m-d") . ": " . $holiday->getName() . "<br>";
}
/*
2020-05-03: 憲法記念日
2020-05-04: みどりの日
2020-05-05: こどもの日
2020-05-06: 振替休日
*/

// ■祝日がない場合、空の配列を返します。
$holidays = $syukujitsu->find(2020, 6);

var_dump($holidays); // array(0) {}
```

+ 年の祝日を取得する<br>
find関数の引数$monthを省略すると、1年分の祝日を取得できます。
```php
$syukujitsu = new Syukujitsu();

// 2020年を指定。※月の引数を省略。
$holidays = $syukujitsu->find(2020);

foreach($holidays as $holiday)
{
    echo $holiday->format("Y-m-d") . ": " . $holiday->getName() . "<br>";
}
/*
2020-01-01: 元日
2020-01-13: 成人の日
2020-02-11: 建国記念の日
2020-02-23: 天皇誕生日
2020-02-24: 振替休日
2020-03-20: 春分の日
2020-04-29: 昭和の日
2020-05-03: 憲法記念日
2020-05-04: みどりの日
2020-05-05: こどもの日
2020-05-06: 振替休日
2020-07-23: 海の日
2020-07-24: スポーツの日
2020-08-10: 山の日
2020-09-21: 敬老の日
2020-09-22: 秋分の日
2020-11-03: 文化の日
2020-11-23: 勤労感謝の日
*/

// ■対象外の年の場合
$holidays = $syukujitsu->find(1900); 

var_dump($holidays);
// array(0) {} ※祝日を取得できない場合、空の配列を返します。
```

**2.日付を指定してチェック**
```php
check(string $time): ?HolidayClass
```
結果は、HolidayClassクラスのインスタントで返されます。HolidayClassクラスはDateTimeクラスの拡張クラスです。<br>
+ 2000年1月1日をチェックする。
```php
$syukujitsu = new Syukujitsu();

$holiday = $syukujitsu->check("2000-01-01");

if( !is_null($holiday) )
{
   echo $holiday->format("Y-m-d") . ": " . $holiday->getName() . "<br>";
}
// 2000-01-01: 元日

// ■祝日でない場合
$holiday = $syukujitsu->check("2000-01-02");

var_dump($holiday); // NULL ※祝日でない場合NULLを返します。
```

## ライセンス
MIT License

## 補足
返値のタイムゾーンはデフォルトのタイムゾーンになります。<br>
タイムゾーンを変更したい場合は、インスタント作成時に設定してください。

```php
$syukujitsu = new Syukujitsu(new DateTimeZone('Asia/Tokyo'));

$holiday = $syukujitsu->check("2000-01-01");

var_dump($holiday);
/*
object(sekidenkiku\syukujitsu\HolidayClass)#6 (4) {
  ["holiday_name":"sekidenkiku\syukujitsu\HolidayClass":private]=>
  string(6) "元日"
  ["date"]=>
  string(26) "2000-01-01 00:00:00.000000"
  ["timezone_type"]=>
  int(3)
  ["timezone"]=>
  string(10) "Asia/Tokyo"
}
*/
```




