<?php

use PHPUnit\Framework\TestCase;
use sekidenkiku\syukujitsu\Syukujitsu;
use sekidenkiku\syukujitsu\HolidayClass;


class SyukujitsuTest extends TestCase
{
    /**
     * @test
     * @throws Exception
     */
    public function holiday_class_names変数の値とHolidayTypeディレクトリ内のクラス名は一致させる()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $property = $reflection->getProperty('holiday_class_names');
        $property->setAccessible(true);
        $holiday_class_names = (array)$property->getValue($holiday);
        sort($holiday_class_names);
        $directory_file_names = $this->getPublicHolidayClassNames();
        sort($directory_file_names);
        $this->assertEquals($holiday_class_names, $directory_file_names);
    }

    /**
     * データ内の日付と日付に挟まれた日付を返します。
     * @test
     * @throws ReflectionException
     */
    public function findPublicHolidaySandwichedDay関数()
    {
        $holiday = new Syukujitsu();

        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('findPublicHolidaySandwichedDay');
        $method->setAccessible(true);
        // 引数が空の場合、空の配列を返す。
        $datetimes = [];
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals([], $val);

        // 対象なしの場合、空の配列を返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-1');
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals([], $val);

        // 対象なしの場合、空の配列を返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-1');
        $datetimes[] = new HolidayClass('2000-1-10');
        $datetimes[] = new HolidayClass('2000-1-20');
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals([], $val);

        // 休日オブジェクトの日付に挟まれる日付がある場合、日付オブジェクトを返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-1');
        $datetimes[] = new HolidayClass('2000-1-3');
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals(new \DateTime('2000-1-2'), $val[0]);

        // 日付が連続している場合は、挟まれていないので、対象にならない。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-1');
        $datetimes[] = new HolidayClass('2000-1-2');
        $datetimes[] = new HolidayClass('2000-1-3');
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals([], $val);

        // 対象が複数の場合、全て返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-1');
        $datetimes[] = new HolidayClass('2000-1-3');
        $datetimes[] = new HolidayClass('2000-2-10');
        $datetimes[] = new HolidayClass('2000-2-12');
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals(new \DateTime('2000-1-2'), $val[0]);
        $this->assertEquals(new \DateTime('2000-2-11'), $val[1]);
    }

    /**
     * データ内の日付が日曜日の場合、他の日付でない次の日付を返します。
     * @test
     * @throws ReflectionException
     */
    public function findSubstituteHoliday関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('findSubstituteHoliday');
        $method->setAccessible(true);

        // 引数が空の場合、空の配列を返す。
        $datetimes = [];
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals([], $val);

        // 対象なしの場合、空の配列を返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-3'); // 月曜日
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals([], $val);

        // 対象なしの場合、空の配列を返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-3'); // 月曜日
        $datetimes[] = new HolidayClass('2000-1-4'); // 火曜日
        $datetimes[] = new HolidayClass('2000-1-5'); // 水曜日
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals([], $val);

        // 日曜の場合、翌日を返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-1'); // 土曜日
        $datetimes[] = new HolidayClass('2000-1-2'); // 日曜日
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals(new \DateTime('2000-1-3'), $val[0]); // 月曜日

        // 翌日が休日オブジェクトの場合、そうでない日の休日オブジェクトを返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-2'); // 日曜日
        $datetimes[] = new HolidayClass('2000-1-3'); // 月曜日
        $datetimes[] = new HolidayClass('2000-1-4'); // 火曜日
        $datetimes[] = new HolidayClass('2000-1-5'); // 水曜日
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals(new \DateTime('2000-1-6'), $val[0]); // 木曜日

        // 対象が複数の場合、全て返す。
        $datetimes = [];
        $datetimes[] = new HolidayClass('2000-1-2'); // 日曜日
        $datetimes[] = new HolidayClass('2000-1-9'); // 日曜日
        $val = $method->invoke($holiday, $datetimes);
        $this->assertEquals(new \DateTime('2000-1-3'), $val[0]); // 月曜日
        $this->assertEquals(new \DateTime('2000-1-10'), $val[1]); // 月曜日
    }

    /**
     * 振替休日のテスト。
     * @test
     * @throws ReflectionException
     */
    public function findHurikaekyujitu関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('findHurikaekyujitu');
        $method->setAccessible(true);

        // 振替休日は1973年4月12日施行なので、1972年は該当なし。
        $data = 1972;
        $expected = [];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

        // 1973年の振替休日。
        $data = 1973;
        $expected = [
            new HolidayClass("1973-4-30"),
            new HolidayClass("1973-9-24"),
        ];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

        // 2000年の振替休日。この年は該当なし。
        $data = 2000;
        $expected = [];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);
    }

    /**
     * 国民の休日のテスト。
     * @test
     * @throws ReflectionException
     */
    public function findKokuminnokyujitu関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('findKokuminnokyujitu');
        $method->setAccessible(true);

        // 国民の休日は、1985年12月27日施行なので、1985年は該当なし。
        $data = 1985;
        $expected = [];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

        // 1986年の国民の休日。該当なし。5/4が該当するが日曜日の為、国民の休日にはならない。
        $data = 1986;
        $expected = [];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

        // 1987年の国民の休日。該当なし。5/4が該当するが振替休日の為、国民の休日にはならない。
        $data = 1987;
        $expected = [];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

        // 1988年の国民の休日。施行後、初めての該当日。
        $data = 1988;
        $expected = [new HolidayClass("1988-5-4"),];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

    }

    /**
     * 国民の祝日のテスト。
     * @test
     * @throws ReflectionException
     */
    public function findPublicHolidays関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('findPublicHolidays');
        $method->setAccessible(true);
        // 祝日法制定(1948年)前は空の配列を返す。
        $data = 1947;
        $expected = [];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

        // 祝日法制定(1948年)。
        $data = 1948;
        $expected = [
            new HolidayClass('1948-09-23'),
            new HolidayClass('1948-11-03'),
            new HolidayClass('1948-11-23'),
        ];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);

        // 祝日法制定の翌年1949年
        $data = 1949;
        $expected = [
            new HolidayClass('1949-01-01'),
            new HolidayClass('1949-01-15'),
            new HolidayClass('1949-03-21'),
            new HolidayClass('1949-04-29'),
            new HolidayClass('1949-05-03'),
            new HolidayClass('1949-05-05'),
            new HolidayClass('1949-09-23'),
            new HolidayClass('1949-11-03'),
            new HolidayClass('1949-11-23'),
        ];
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);
    }

    /**
     * 日付の重複チェック。
     * @test
     * @throws ReflectionException
     */
    public function isDuplicated関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('isDuplicated');
        $method->setAccessible(true);
        // 重複していない場合。
        $search = new \DateTime('2000-1-1');
        $holidays = [];
        $actual = $method->invoke($holiday, $search, $holidays);
        $this->assertFalse($actual);

        // 重複していない場合。
        $search = new \DateTime('2000-1-1');
        $holidays = [
            new HolidayClass('2000-1-2'),
            new HolidayClass('2000-1-3'),
        ];
        $actual = $method->invoke($holiday, $search, $holidays);
        $this->assertFalse($actual);

        // 重複している場合。
        $search = new \DateTime('2000-1-1');
        $holidays = [
            new HolidayClass('2000-1-1'),
            new HolidayClass('2000-1-2'),
            new HolidayClass('2000-1-3'),
        ];
        $actual = $method->invoke($holiday, $search, $holidays);
        $this->assertTrue($actual);
    }

    /**
     * キャッシュ機能のバッファ取得。
     * @test
     * @throws ReflectionException
     */
    public function addCache関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('addCache');
        $method->setAccessible(true);

        // データ追加
        $buff = [];
        $name = "2000";
        $data = ['a'];
        $actual = $method->invoke($holiday, $buff, $name, $data);
        $expected = [
            2000 => ['a'],
        ];
        $this->assertSame($expected, $actual);

        // さらに追加
        $buff = [
            2000 => ['a'],
        ];
        $name = "2001";
        $data = ['b'];
        $actual = $method->invoke($holiday, $buff, $name, $data);
        $expected = [
            2000 => ['a'],
            2001 => ['b'],
        ];
        $this->assertSame($expected, $actual);

        // 10件以上のキャッシュは削除する。
        $buff = [
            2000 => ['a'],
            2001 => ['b'],
            2002 => ['c'],
            2003 => ['d'],
            2004 => ['e'],
            2005 => ['f'],
            2006 => ['g'],
            2007 => ['h'],
            2008 => ['i'],
            2009 => ['j'],
        ];
        $name = "2010";
        $data = ['k'];
        $actual = $method->invoke($holiday, $buff, $name, $data);
        $expected = [
            2001 => ['b'],
            2002 => ['c'],
            2003 => ['d'],
            2004 => ['e'],
            2005 => ['f'],
            2006 => ['g'],
            2007 => ['h'],
            2008 => ['i'],
            2009 => ['j'],
            2010 => ['k'],
        ];
        $this->assertSame($expected, $actual);
    }

    /**
     * 指定された年の休日(祝日、振替休日、国民の休日)を返す。
     * @test
     * @throws ReflectionException
     */
    public function findYearHoliday関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method = $reflection->getMethod('findYearHoliday');
        $method->setAccessible(true);

        $data = [
            ['2019-01-01', '元日'],
            ['2019-01-14', '成人の日'],
            ['2019-02-11', '建国記念の日'],
            ['2019-03-21', '春分の日'],
            ['2019-04-29', '昭和の日'],
            ['2019-04-30', '国民の休日'],
            ['2019-05-01', '即位の日'],
            ['2019-05-02', '国民の休日'],
            ['2019-05-03', '憲法記念日'],
            ['2019-05-04', 'みどりの日'],
            ['2019-05-05', 'こどもの日'],
            ['2019-05-06', '振替休日'],
            ['2019-07-15', '海の日'],
            ['2019-08-11', '山の日'],
            ['2019-08-12', '振替休日'],
            ['2019-09-16', '敬老の日'],
            ['2019-09-23', '秋分の日'],
            ['2019-10-14', '体育の日'],
            ['2019-10-22', '即位礼正殿の儀'],
            ['2019-11-03', '文化の日'],
            ['2019-11-04', '振替休日'],
            ['2019-11-23', '勤労感謝の日'],
        ];
        $expected = [];
        foreach ($data as $value) {
            $expected[] = new HolidayClass($value[0]);
        }

        $data = 2019;
        $actual = $method->invoke($holiday, $data);
        $this->assertEquals($expected, $actual);
    }

    /**
     * 指定された月内の祝日を抽出して返す。
     * @test
     * @throws ReflectionException
     */
    public function extractMonthHoliday関数()
    {
        $holiday = new Syukujitsu();
        $reflection = new \ReflectionClass($holiday);
        $method1 = $reflection->getMethod('findYearHoliday');
        $method1->setAccessible(true);
        $holidays = $method1->invoke($holiday, 2019);

        $data = [
            ['2019-05-01', '即位の日'],
            ['2019-05-02', '国民の休日'],
            ['2019-05-03', '憲法記念日'],
            ['2019-05-04', 'みどりの日'],
            ['2019-05-05', 'こどもの日'],
            ['2019-05-06', '振替休日'],
        ];
        $expected = [];
        foreach ($data as $value) {
            $expected[] = new HolidayClass($value[0]);
        }
        $method2 = $reflection->getMethod('extractMonthHoliday');
        $method2->setAccessible(true);
        $actual = $method2->invoke($holiday, $holidays, 2019, 5);
        $actual = array_values($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * 指定された年の休日（祝日、振替休日、国民の休日）を返す。
     * @test
     * @throws Exception
     */
    public function find関数()
    {
        $data = [
            ['2019-01-01', '元日'],
            ['2019-01-14', '成人の日'],
            ['2019-02-11', '建国記念の日'],
            ['2019-03-21', '春分の日'],
            ['2019-04-29', '昭和の日'],
            ['2019-04-30', '国民の休日'],
            ['2019-05-01', '即位の日'],
            ['2019-05-02', '国民の休日'],
            ['2019-05-03', '憲法記念日'],
            ['2019-05-04', 'みどりの日'],
            ['2019-05-05', 'こどもの日'],
            ['2019-05-06', '振替休日'],
            ['2019-07-15', '海の日'],
            ['2019-08-11', '山の日'],
            ['2019-08-12', '振替休日'],
            ['2019-09-16', '敬老の日'],
            ['2019-09-23', '秋分の日'],
            ['2019-10-14', '体育の日'],
            ['2019-10-22', '即位礼正殿の儀'],
            ['2019-11-03', '文化の日'],
            ['2019-11-04', '振替休日'],
            ['2019-11-23', '勤労感謝の日'],
        ];
        $holiday = new Syukujitsu();
        $val = $holiday->find(2019);
        foreach ($val as $key => $value) {
            $this->assertEquals($data[$key][0], $value->format('Y-m-d'));
            $this->assertEquals($data[$key][1], $value->getName());
        }

        // 祝日法制定前は空の配列を返す。
        $val = $holiday->find(1947);
        $this->assertEquals([], $val);

        // 祝日法制定1948年
        $data = [
            ['1948-09-23', '秋分の日'],
            ['1948-11-03', '文化の日'],
            ['1948-11-23', '勤労感謝の日'],
        ];
        $val = $holiday->find(1948);
        foreach ($val as $key => $value) {
            $this->assertEquals($data[$key][0], $value->format('Y-m-d'));
            $this->assertEquals($data[$key][1], $value->getName());
        }
        // 祝日法制定の翌年1949年
        $data = [
            ['1949-01-01', '元日'],
            ['1949-01-15', '成人の日'],
            ['1949-03-21', '春分の日'],
            ['1949-04-29', '天皇誕生日'],
            ['1949-05-03', '憲法記念日'],
            ['1949-05-05', 'こどもの日'],
            ['1949-09-23', '秋分の日'],
            ['1949-11-03', '文化の日'],
            ['1949-11-23', '勤労感謝の日'],
        ];
        $val = $holiday->find(1949);
        foreach ($val as $key => $value) {
            $this->assertEquals($data[$key][0], $value->format('Y-m-d'));
            $this->assertEquals($data[$key][1], $value->getName());
        }
    }

    /**
     * 指定された月の休日（祝日、振替休日、国民の休日）を返す。
     * @test
     * @throws Exception
     */
    public function find関数（月指定）()
    {
        // 指定月の祝日を返す。
        $data = [
            ['2010-01-01', '元日'],
            ['2010-01-11', '成人の日'],
        ];
        $holiday = new Syukujitsu();
        $val = $holiday->find(2010, 1);
        foreach ($val as $key => $value) {
            $this->assertEquals($data[$key][0], $value->format('Y-m-d'));
            $this->assertEquals($data[$key][1], $value->getName());
        }

        $data = [
            ['2019-05-01', '即位の日'],
            ['2019-05-02', '国民の休日'],
            ['2019-05-03', '憲法記念日'],
            ['2019-05-04', 'みどりの日'],
            ['2019-05-05', 'こどもの日'],
            ['2019-05-06', '振替休日'],
        ];
        $holiday = new Syukujitsu();
        $val = $holiday->find(2019, 5);
        foreach ($val as $key => $value) {
            $this->assertEquals($data[$key][0], $value->format('Y-m-d'));
            $this->assertEquals($data[$key][1], $value->getName());
        }

        // 祝日が無い場合、空の配列を返す。
        $val = $holiday->find(2019, 12);
        $this->assertEquals([], $val);

        // 引数が無効な数字の場合、空の配列を返す。
        $val = $holiday->find(2019, 13);
        $this->assertEquals([], $val);
    }

    /**
     * 指定された月日が休日（祝日、振替休日、国民の休日）かどうかチェックする。
     * @test
     * @throws Exception
     */
    public function check関数()
    {
        $holiday = new Syukujitsu();
        // 国民の祝日
        $val = $holiday->check('2019-1-1');
        $this->assertEquals('元日', $val->getName());

        // 振替休日
        $val = $holiday->check('2019-5-6');
        $this->assertEquals('振替休日', $val->getName());

        // 国民の休日(1985年12月27日施行)
        $val = $holiday->check('1985-5-4'); // 1985年5月時点は休日でない。
        $this->assertNull($val);

        // 国民の休日(日曜日の場合は除く。)
        $val = $holiday->check('1986-5-4'); // 日曜日
        $this->assertNull($val);

        /**
         * 振替休日と国民の休日の両方に当てはまる場合は、振替休日とする。(当サイトのルール)
         * 1987年5月
         * 3(日)祝日
         * 4(月)振替休日
         * 5(火)祝日
         */
        $val = $holiday->check('1987-5-4');
        $this->assertEquals('振替休日', $val->getName());

        // 国民の休日(日曜日の場合は除く。)
        $val = $holiday->check('2003-5-4'); // 日曜日
        $this->assertNull($val);

        // 国民の休日
        $val = $holiday->check('2019-4-30');
        $this->assertEquals('国民の休日', $val->getName());
    }

    /**
     * 日付に時刻が入力されていても動作する。結果の時刻は、00:00:00になる。
     * @test
     * @throws Exception
     */
    public function check関数2()
    {
        $holiday = new Syukujitsu();
        // 日付に時刻が指定されていても、祝日が表示される。
        $val = $holiday->check('2019-1-1 9:00:00');
        $this->assertEquals('元日', $val->getName());
        $this->assertEquals('2019-01-01 00:00:00', $val->format('Y-m-d H:i:s'));
    }

    /**
     * HolydayClassクラスインスタントのタイムゾーンが変更されている。
     * @test
     * @throws Exception
     */
    public function タイムゾーン設定()
    {
        // 現在のタイムゾーンが適用される。check関数。
        $expected = date_default_timezone_get();
        $syukujitsu = new Syukujitsu();
        $holiday = $syukujitsu->check('2019-1-1');
        $tz = $holiday->getTimezone();
        $this->assertEquals($expected, $tz->getName());

        // 現在のタイムゾーンが適用される。find関数。
        $holidays = $syukujitsu->find('2019');
        $tz = $holidays[0]->getTimezone();
        $this->assertEquals($expected, $tz->getName());

        // 設定したタイムゾーンが適用される。check関数。
        $expected = 'Asia/Tokyo';
        $syukujitsu = new Syukujitsu(new \DateTimeZone($expected));
        $holiday = $syukujitsu->check('2019-1-1');
        $tz = $holiday->getTimezone();
        $this->assertEquals($expected, $tz->getName());

        // 設定したタイムゾーンが適用される。find関数。
        $holidays = $syukujitsu->find('2019');
        $tz = $holidays[0]->getTimezone();
        $this->assertEquals($expected, $tz->getName());
    }

    /**
     * HolidayTypeディレクトリ配下のクラス名を取得します。
     * @return string[] クラス名の配列。
     */
    protected function getPublicHolidayClassNames(): array
    {
        $dir = __DIR__ . '/../src/HolidayTypes';
        $result = [];
        $iterator = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($iterator);
        foreach ($iterator as $file_info) { // $fileinfoはSplFiIeInfoオブジェクト
            if ($file_info->isFile()) {
                $class_name = $file_info->getFilename();
                if (preg_match('/^(.+)\.php/', $class_name, $matches)) {
                    $class_name = isset($matches[1]) ? $matches[1] : '';
                } else {
                    $class_name = '';
                }
                if ($class_name) {
                    $result[] = $class_name;
                }
            }
        }
        return $result;
    }
}
