<?php
/**
 * 国民の祝日取得クラス。
 * 日本の国民の祝日を取得します。
 * @copyright (c) Takahisa Ishida <sekidenkiku@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/sekidenkiku/syukujitsu
 * @version 1.0.1
 */

namespace sekidenkiku\syukujitsu;
class Syukujitsu
{
    /**
     * @var array 祝日作成クラスの名前一覧(HolidayTypesディレクトリ内のファイル名)
     */
    private $holiday_class_names = [
        'Bunkanohi',
        'Ganjitsu',
        'Keironohi',
        'Kenkokukinennohi',
        'Kenpokinenbi',
        'Kinrokansyanohi',
        'Kodomonohi',
        'Koutaisiakihitosinnounokekonnogi',
        'Koutaisinaruhitosinnounokekonnogi',
        'Midorinohi',
        'Seijinnohi',
        'Sokuinohi',
        'Sokuireiseidennogi',
        'Supotunohi',
        'Syowanohi',
        'Syowatennounotaisonorei',
        'Syubunnohi',
        'Syunbunnohi',
        'Taiikunohi',
        'Tennotanjyobi',
        'Uminohi',
        'Yamanohi',
    ];

    /**
     * @var array 休日情報のキャッシュ。
     */
    private $holiday_cache = [];

    /**
     * @var array 祝日情報のキャッシュ。
     */
    private $public_holiday_cache = [];

    /**
     * @var \DateTimeZone タイムゾーン。
     */
    private $timezone;

    /**
     * Syukujitsu constructor.
     * @param \DateTimeZone|null $timezone タイムゾーンオブジェクト。指定しない場合、デフォルトタイムゾーンになります。
     */
    public function __construct(\DateTimeZone $timezone = null)
    {
        if ($timezone instanceof \DateTimeZone) {
            $this->timezone = $timezone;
        } else {
            $str = date_default_timezone_get();
            $this->timezone = new \DateTimeZone($str);
        }
    }

    /**
     * クラス内のタイムゾーンを設定します。
     * @param \DateTimeZone $timezone タイムゾーン。
     */
    public function setTimezone(\DateTimeZone $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * クラス内のタイムゾーンを返します。
     * @return \DateTimeZone タイムゾーン。
     */
    public function getTimezone(): \DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * 指定された日付が休日(国民の祝日、振替休日、国民の休日)の場合、休日を返します。
     * @param string $time 日付の文字列。
     * @return HolidayClass|null 休日オブジェクト。休日でない場合nullを返します。
     * @throws \Exception
     */
    public function check(string $time): ?HolidayClass
    {
        $datetime = new \DateTime($time, $this->timezone);
        $datetime->setTime(0, 0, 0);
        $year = intval($datetime->format("Y"));
        $result = null;
        $holidays = $this->getHolidays($year);
        foreach ($holidays as $holiday) {
            if ($datetime == $holiday) {
                $result = $holiday;
                break;
            }
        }
        return $result;
    }

    /**
     * 指定された年、月の休日(国民の祝日、振替休日、国民の休日)の一覧を返します。
     * @param int $year 西暦。
     * @param int|null $month 月。省略した場合、1～12月の休日を返します。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    public function getHolidays(int $year, ?int $month = null): array
    {
        if (isset($this->holiday_cache[$year])) {
            $result = $this->holiday_cache[$year];
        } else {
            $holidays = $this->getPublicHolidays($year);
            // 振替休日の取得。(振替休日と国民の休日の両方の条件を満たす場合は振替休日とする。そのため国民の休日より先に追加する)
            $holidays = array_merge($holidays, $this->getHurikaekyujitu($year));

            // 国民の休日取得。
            foreach ($this->getKokuminnokyujitu($year) as $val) {
                if (false === $this->hasDate($val, $holidays)) {
                    $holidays[] = $val;
                }
            }
            sort($holidays);
            // キャッシュの保存。
            $this->holiday_cache[$year] = $holidays;
            // キャッシュのサイズ制限。古いキャッシュ(配列先頭の値)を削除。※array_shift()で削除すると処理が遅くなるため、unset()を使用している。
            if (count($this->holiday_cache) > 10) {
                reset($this->holiday_cache);
                unset($this->holiday_cache[key($this->holiday_cache)]);
            }
            $result = $holidays;
        }
        // 月外の休日を削除。
        if (!is_null($month)) {
            if (1 <= $month && $month <= 12) {
                $first = new \DateTime("{$year}-{$month}-1", $this->timezone);
                $last = new \DateTime("last day of {$year}-{$month}", $this->timezone);
                /**
                 * @var $holiday HolidayClass 祝日オブジェクト
                 */
                foreach ($result as $key => $holiday) {
                    if ($first > $holiday || $last < $holiday) {
                        unset($result[$key]);
                    }
                }
            } else {
                $result = [];
            }
        }
        return array_values($result);
    }

    /**
     * 指定された年の国民の祝日を返します。
     * @param int $year 西暦。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function getPublicHolidays(int $year): array
    {
        if (isset($this->public_holiday_cache[$year])) {
            return $this->public_holiday_cache[$year];
        } else {
            $result = [];
            // 祝日の判定
            foreach ($this->holiday_class_names as $class_name) {
                $class_name = 'sekidenkiku\syukujitsu\HolidayTypes\\' . $class_name;
                /**
                 * @var HolidayTypeAbstract $obj
                 */
                $obj = new $class_name($this->timezone);
                if (!is_null($date = $obj->getDate($year))) {
                    $holiday = new HolidayClass($date->format('Y-m-d'), $this->timezone);
                    $holiday->setName($obj->getName());
                    $result[] = $holiday;
                }
            }
            sort($result);
            // キャッシュの保存。
            $this->public_holiday_cache[$year] = $result;
            // キャッシュのサイズ制限。古いキャッシュ(配列先頭の値)を削除。※array_shift()で削除すると処理が遅くなるため、unset()を使用している。
            if (count($this->public_holiday_cache) > 10) {
                reset($this->public_holiday_cache);
                unset($this->public_holiday_cache[key($this->public_holiday_cache)]);
            }
            return $result;
        }
    }

    /**
     * 指定された年の振替休日を返します。
     * @param int $year 西暦。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function getHurikaekyujitu(int $year): array
    {
        // @TODO 年をまたぐ振替休日に対応していない。将来12月31日が祝日になった場合は修正する。
        // 振替休日は、1973年4月12日施行のため、1972年以前は存在しない。
        // また、1973年の場合、2月11日(日)の建国記念日は、振替休日とならないので、2月12日の振替休日を除外する。
        if ($year <= 1972) {
            $result = [];
        } else {
            $result = $this->getSubstituteHoliday($this->getPublicHolidays($year));
            if (1973 == $year) {
                foreach ($result as $key => $value) {
                    if ($value == new \DateTime("1973-2-12", $this->timezone)) {
                        unset($result[$key]);
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 指定された年の国民の休日を返します。
     * @param int $year 西暦。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function getKokuminnokyujitu(int $year): array
    {
        // @TODO 年をまたぐ国民の休日に対応していない。将来12月30日が祝日となり1月1日(祝日)と挟まれた場合は修正する。
        // 国民の休日は、1985年12月27日施行のため1985年以前は存在しない。
        if ($year <= 1985) {
            $result = [];
        } else {
            $result = $this->getPublicHolidaySandwichedDay($this->getPublicHolidays($year));
            if ($year <= 2006) {
                // 2006年12月31日以前は日曜日は国民の休日にならないため除外する。
                foreach ($result as $key => $value) {
                    if (
                        $value < new \DateTime("2006-12-31", $this->timezone)
                        && 0 == $value->format('w')
                    ) {
                        unset($result[$key]);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 日曜日の休日オブジェクトがある場合、次の休日オブジェクトでない日を「振替休日」として返します。
     * @param HolidayClass[] $holidays 休日オブジェクトの配列。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function getSubstituteHoliday(array $holidays): array
    {
        $result = [];
        foreach ($holidays as $holiday) {
            if ("0" === $holiday->format('w')) {
                $date2 = clone $holiday;
                do {
                    $date2->modify('+1 days');
                } while ($this->hasDate($date2, $holidays));
                $obj = new HolidayClass($date2->format('Y-m-d'), $this->timezone);
                $obj->setName('振替休日');
                $result[] = $obj;
            }
        }
        return $result;
    }

    /**
     * 前後を休日オブジェクトで挟まれた日を国民の休日として返します。
     * @param HolidayClass[] $holidays 休日オブジェクトの配列。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function getPublicHolidaySandwichedDay(array $holidays): array
    {
        $result = [];
        foreach ($holidays as $key => $holiday) {
            $date2 = clone $holiday;
            $date2->modify('+1 days');
            $date3 = clone $holiday;
            $date3->modify('+2 days');
            if (true === $this->hasDate($date3, $holidays) && false === $this->hasDate($date2, $holidays)) {
                $obj = new HolidayClass($date2->format('Y-m-d'), $this->timezone);
                $obj->setName('国民の休日');
                $result[] = $obj;
            }
        }
        return $result;
    }

    /**
     * 休日オブジェクト内に重複する日付がないかをチェックします。
     * @param \DateTime $search 検索する日付。
     * @param HolidayClass[] $holidays 休日オブジェクトの配列。
     * @return bool 一致する日付が見つかった場合trueを返します。
     */
    private function hasDate(\DateTime $search, array $holidays): bool
    {
        foreach ($holidays as $holiday) {
            if ($search == $holiday) {
                return true;
            }
        }
        return false;
    }
}