<?php

use PHPUnit\Framework\TestCase;
use sekidenkiku\syukujitsu\HolidayTypes;


class HolidayTypesTest extends TestCase
{
    const MAX_YEAR = 2100;

    /**
     * 元日    1月1日
     * @test
     */
    public function 元日()
    {
        // 正しい名前が返される。
        $type = new HolidayTypes\Ganjitsu();
        $this->assertEquals('元日', $type->getName());

        // 法律施行前はnullを返す。
        $year = 1948;
        $this->assertNull($type->findDate($year));
        // 法律施行後はDateTimeオブジェクトを返す。
        $year = rand(1949, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-1-1"), $type->findDate($year));
    }

    /**
     * 成人の日    1月の第2月曜日
     * @test
     */
    public function 成人の日()
    {
        $type = new HolidayTypes\Seijinnohi();
        $this->assertEquals('成人の日', $type->getName());

        $year = 1948;
        $this->assertNull($type->findDate($year));
        // 1949年から1月15日
        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-1-15"), $type->findDate($year));
        $year = 1999;
        $this->assertEquals(new \DateTime("{$year}-1-15"), $type->findDate($year));
        // 2000年以降、1月の第2月曜日
        $year = rand(2000, self::MAX_YEAR);
        $expected = date('Y-m-d', strtotime("second mon of jan {$year}"));
        $this->assertEquals(new \DateTime($expected), $type->findDate($year));
    }

    /**
     * 建国記念の日    政令で定める日 2月11日
     * @test
     */
    public function 建国記念の日()
    {
        $type = new HolidayTypes\Kenkokukinennohi();
        $this->assertEquals('建国記念の日', $type->getName());

        $year = 1966;
        $this->assertNull($type->findDate($year));

        // 1967年から2月11日
        $year = rand(1967, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-2-11"), $type->findDate($year));
    }

    /**
     * 春分の日    春分日
     * @test
     */
    public function 春分の日()
    {
        $type = new HolidayTypes\Syunbunnohi();
        $this->assertEquals('春分の日', $type->getName());

        $year = 1948;
        $this->assertNull($type->findDate($year));

        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-3-21"), $type->findDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-3-20"), $type->findDate($year));
    }

    /**
     * 昭和の日    4月29日
     * @test
     */
    public function 昭和の日()
    {
        $type = new HolidayTypes\Syowanohi();
        $this->assertEquals('昭和の日', $type->getName());

        $year = 2006;
        $this->assertNull($type->findDate($year));
        // 2007年から4月29日
        $year = rand(2007, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->findDate($year));
    }

    /**
     * 憲法記念日    5月3日
     * @test
     */
    public function 憲法記念日()
    {
        $type = new HolidayTypes\Kenpokinenbi();
        $this->assertEquals('憲法記念日', $type->getName());

        $year = 1948;
        $this->assertNull($type->findDate($year));
        // 1949年から5月3日
        $year = rand(1949, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-5-3"), $type->findDate($year));
    }

    /**
     * みどりの日    5月4日
     * @test
     */
    public function みどりの日()
    {
        $type = new HolidayTypes\Midorinohi();
        $this->assertEquals('みどりの日', $type->getName());

        $year = 1988;
        $this->assertNull($type->findDate($year));
        // 1989年から2006年まで4月29日。
        $year = rand(1989, 2006);
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->findDate($year));
        // 2007年から5月4日。
        $year = rand(2007, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-5-4"), $type->findDate($year));
    }

    /**
     * こどもの日    5月5日
     * @test
     */
    public function こどもの日()
    {
        $type = new HolidayTypes\Kodomonohi();
        $this->assertEquals('こどもの日', $type->getName());

        $year = 1948;
        $this->assertNull($type->findDate($year));
        // 1949年から5月5日。
        $year = rand(1949, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-5-5"), $type->findDate($year));
    }

    /**
     * 海の日    7月の第3月曜日
     * @test
     */
    public function 海の日()
    {
        $type = new HolidayTypes\Uminohi();
        $this->assertEquals('海の日', $type->getName());

        $year = 1955;
        $this->assertNull($type->findDate($year));
        // 1996年から2002年まで7月20日。
        $year = rand(1996, 2002);
        $this->assertEquals(new \DateTime("{$year}-7-20"), $type->findDate($year));
        // 2003年から7月の第3月曜日。
        $year = rand(2003, 2019);
        $expected = date('Y-m-d', strtotime("third mon of jul {$year}"));
        $this->assertEquals(new \DateTime($expected), $type->findDate($year));
        $year = 2010;
        $this->assertEquals(new \DateTime("{$year}-7-19"), $type->findDate($year));
        // 2020年は7月23日。五輪・パラリンピック特別措置法
        $year = 2020;
        $this->assertEquals(new \DateTime("{$year}-7-23"), $type->findDate($year));
        // 2021年は7月22日。五輪・パラリンピック特別措置法改正
        $year = 2021;
        $this->assertEquals(new \DateTime("{$year}-7-22"), $type->findDate($year));
        // 2022年から元に戻る。7月の第3月曜日。
        $year = rand(2022, self::MAX_YEAR);
        $expected = date('Y-m-d', strtotime("third mon of jul {$year}"));
        $this->assertEquals(new \DateTime($expected), $type->findDate($year));
    }

    /**
     * 山の日    8月11日
     * @test
     */
    public function 山の日()
    {
        $type = new HolidayTypes\Yamanohi();
        $this->assertEquals('山の日', $type->getName());

        $year = 2015;
        $this->assertNull($type->findDate($year));
        // 2016年から8月11日。
        $year = rand(2016, 2019);
        $this->assertEquals(new \DateTime("{$year}-8-11"), $type->findDate($year));
        // 2020年は8月10日。五輪・パラリンピック特別措置法
        $year = 2020;
        $this->assertEquals(new \DateTime("{$year}-8-10"), $type->findDate($year));
        // 2021年は8月8日。五輪・パラリンピック特別措置法改正
        $year = 2021;
        $this->assertEquals(new \DateTime("{$year}-8-8"), $type->findDate($year));
        // 2022年から戻る。8月11日。
        $year = rand(2022, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-8-11"), $type->findDate($year));
    }

    /**
     * 敬老の日    9月の第3月曜日
     * @test
     */
    public function 敬老の日()
    {
        $type = new HolidayTypes\Keironohi();
        $this->assertEquals('敬老の日', $type->getName());

        $year = 1965;
        $this->assertNull($type->findDate($year));
        // 1966年から9月15日。
        $year = rand(1966, 2002);
        $this->assertEquals(new \DateTime("{$year}-9-15"), $type->findDate($year));
        // 2003年から9月の第3月曜日。
        $year = rand(2003, self::MAX_YEAR);
        $year = 2003;
        $expected = date('Y-m-d', strtotime("third mon of sep {$year}"));
        $this->assertEquals(new \DateTime($expected), $type->findDate($year));
    }

    /**
     * 秋分の日    秋分日
     * @test
     */
    public function 秋分の日()
    {
        $type = new HolidayTypes\Syubunnohi();
        $this->assertEquals('秋分の日', $type->getName());

        $year = 1947;
        $this->assertNull($type->findDate($year));

        $year = 1948;
        $this->assertEquals(new \DateTime("{$year}-9-23"), $type->findDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-9-23"), $type->findDate($year));

    }

    /**
     * 体育の日    10月の第2月曜日
     * @test
     */
    public function 体育の日()
    {
        $type = new HolidayTypes\Taiikunohi();
        $this->assertEquals('体育の日', $type->getName());

        $year = 1965;
        $this->assertNull($type->findDate($year));
        // 1966年から10月10日。
        $year = rand(1966, 1999);
        $this->assertEquals(new \DateTime("{$year}-10-10"), $type->findDate($year));
        // 2000年から10月の第2月曜日。
        $year = rand(2000, 2019);
        $expected = date('Y-m-d', strtotime("second mon of oct {$year}"));
        $this->assertEquals(new \DateTime($expected), $type->findDate($year));
        // 2020年からスポーツの日に改名。2020年以降、体育の日はない。
        $year = rand(2020, self::MAX_YEAR);
        $this->assertNull($type->findDate($year));
    }

    /**
     * スポーツの日    10月の第2月曜日
     * @test
     */
    public function スポーツの日()
    {
        $type = new HolidayTypes\Supotunohi();
        $this->assertEquals('スポーツの日', $type->getName());

        $year = 2019;
        $this->assertNull($type->findDate($year));
        // 2020年から10月の第2月曜日。
        // 2020年は7月24日。五輪・パラリンピック特別措置法
        $year = 2020;
        $this->assertEquals(new \DateTime("{$year}-7-24"), $type->findDate($year));
        // 2021年は7月23日。五輪・パラリンピック特別措置法改正
        $year = 2021;
        $this->assertEquals(new \DateTime("{$year}-7-23"), $type->findDate($year));
        // 2022年から戻る。10月の第2月曜日。
        $year = rand(2022, self::MAX_YEAR);
        $expected = date('Y-m-d', strtotime("second mon of oct {$year}"));
        $this->assertEquals(new \DateTime($expected), $type->findDate($year));
    }

    /**
     * 文化の日    11月3日
     * @test
     */
    public function 文化の日()
    {
        $type = new HolidayTypes\Bunkanohi();
        $this->assertEquals('文化の日', $type->getName());

        $year = 1947;
        $this->assertNull($type->findDate($year));
        // 1948年から11月3日。
        $year = rand(1948, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-11-3"), $type->findDate($year));
    }

    /**
     * 勤労感謝の日    11月23日
     * @test
     */
    public function 勤労感謝の日()
    {
        $type = new HolidayTypes\Kinrokansyanohi();
        $this->assertEquals('勤労感謝の日', $type->getName());

        $year = 1947;
        $this->assertNull($type->findDate($year));
        // 1948年から11月23日。
        $year = rand(1948, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-11-23"), $type->findDate($year));
    }

    /**
     * 天皇誕生日    12月23日
     * @test
     */
    public function 天皇誕生日()
    {
        $type = new HolidayTypes\Tennotanjyobi();
        $this->assertEquals('天皇誕生日', $type->getName());

        $year = 1948;
        $this->assertNull($type->findDate($year));
        // 1949年から11月23日。
        $year = rand(1949, 1988);
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->findDate($year));
        // 1989年から12月23日
        $year = rand(1989, 2018);
        $this->assertEquals(new \DateTime("{$year}-12-23"), $type->findDate($year));
        // 2019年は祝日無し
        $year = 2019;
        $this->assertNull($type->findDate($year));
        // 2020年以降は2月23日
        $year = rand(2020, self::MAX_YEAR);
        $this->assertEquals(new \DateTime("{$year}-2-23"), $type->findDate($year));
    }

    /**
     * 皇太子明仁親王の結婚の儀
     */
    public function 皇太子明仁親王の結婚の儀()
    {
        $type = new Koutaisiakihitosinnounokekonnogi();
        $this->assertEquals('皇太子明仁親王の結婚の儀', $type->getName());

        $year = 1958;
        $this->assertNull($type->findDate($year));

        $year = 1959;
        $this->assertEquals(new \DateTime("{$year}-4-10"), $type->findDate($year));

        $year = 1960;
        $this->assertNull($type->findDate($year));
    }

    /**
     * 皇太子明仁親王の結婚の儀
     */
    public function 皇太子徳仁親王の結婚の儀()
    {
        $type = new Koutaisinaruhitosinnounokekonnogi();
        $this->assertEquals('皇太子徳仁親王の結婚の儀', $type->getName());

        $year = 1992;
        $this->assertNull($type->findDate($year));

        $year = 1993;
        $this->assertEquals(new \DateTime("{$year}-6-9"), $type->findDate($year));

        $year = 1994;
        $this->assertNull($type->findDate($year));

    }

    /**
     * 昭和天皇の大喪の礼
     */
    public function 昭和天皇の大喪の礼()
    {
        $type = new Koutaisinaruhitosinnounokekonnogi();
        $this->assertEquals('昭和天皇の大喪の礼', $type->getName());

        $year = 1988;
        $this->assertNull($type->findDate($year));

        $year = 1989;
        $this->assertEquals(new \DateTime("{$year}-2-24"), $type->findDate($year));

        $year = 1990;
        $this->assertNull($type->findDate($year));
    }

    /**
     * 即位の日
     * @test
     */
    public function 即位の日()
    {
        $type = new HolidayTypes\Sokuinohi();
        $this->assertEquals('即位の日', $type->getName());

        $year = 2018;
        $this->assertNull($type->findDate($year));

        $year = 2019;
        $this->assertEquals(new \DateTime("{$year}-5-1"), $type->findDate($year));

        $year = 2020;
        $this->assertNull($type->findDate($year));

    }

    /**
     * 即位礼正殿の儀
     * @test
     */
    public function 即位礼正殿の儀()
    {
        $type = new HolidayTypes\Sokuireiseidennogi();
        $this->assertEquals('即位礼正殿の儀', $type->getName());

        $year = 1989;
        $this->assertNull($type->findDate($year));

        $year = 1990;
        $this->assertEquals(new \DateTime("{$year}-11-12"), $type->findDate($year));

        $year = 1991;
        $this->assertNull($type->findDate($year));

        $year = 2018;
        $this->assertNull($type->findDate($year));

        $year = 2019;
        $this->assertEquals(new \DateTime("{$year}-10-22"), $type->findDate($year));

        $year = 2020;
        $this->assertNull($type->findDate($year));
    }

}
