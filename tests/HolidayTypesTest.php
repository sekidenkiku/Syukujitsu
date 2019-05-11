<?php
use PHPUnit\Framework\TestCase;
use sekidenkiku\syukujitsu\HolidayTypes;


class HolidayTypesTest extends TestCase
{
    /**
     * 元日    1月1日
     * @test
     */
    public function 元日()
    {
        $type = new HolidayTypes\Ganjitsu();
        $this->assertEquals('元日', $type->getName());

        $year = 1948;
        $this->assertNull($type->getDate($year));
        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-1-1"), $type->getDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-1-1"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));
        // 1949年から1月15日
        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-1-15"), $type->getDate($year));
        $year = 1999;
        $this->assertEquals(new \DateTime("{$year}-1-15"), $type->getDate($year));
        // 2000年以降、1月の第2月曜日
        $year = 2000;
        $this->assertEquals(new \DateTime("{$year}-1-10"), $type->getDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-1-8"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        // 1967年から2月11日
        $year = 1967;
        $this->assertEquals(new \DateTime("{$year}-2-11"), $type->getDate($year));
        $year = 2000;
        $this->assertEquals(new \DateTime("{$year}-2-11"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-3-21"), $type->getDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-3-20"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 2007;
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->getDate($year));
        $year = 2010;
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));


        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-5-3"), $type->getDate($year));
        $year = 2000;
        $this->assertEquals(new \DateTime("{$year}-5-3"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1989;
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->getDate($year));
        $year = 2006;
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->getDate($year));
        $year = 2007;
        $this->assertEquals(new \DateTime("{$year}-5-4"), $type->getDate($year));
        $year = 2010;
        $this->assertEquals(new \DateTime("{$year}-5-4"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-5-5"), $type->getDate($year));
        $year = 2000;
        $this->assertEquals(new \DateTime("{$year}-5-5"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1996;
        $this->assertEquals(new \DateTime("{$year}-7-20"), $type->getDate($year));
        $year = 2002;
        $this->assertEquals(new \DateTime("{$year}-7-20"), $type->getDate($year));
        // 2003年から7月の第3月曜日。
        $year = 2003;
        $this->assertEquals(new \DateTime("{$year}-7-21"), $type->getDate($year));
        $year = 2010;
        $this->assertEquals(new \DateTime("{$year}-7-19"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 2016;
        $this->assertEquals(new \DateTime("{$year}-8-11"), $type->getDate($year));
        $year = 2019;
        $this->assertEquals(new \DateTime("{$year}-8-11"), $type->getDate($year));
        $year = 2020;
        $this->assertEquals(new \DateTime("{$year}-8-10"), $type->getDate($year));
        $year = 2021;
        $this->assertEquals(new \DateTime("{$year}-8-11"), $type->getDate($year));
        $year = 2030;
        $this->assertEquals(new \DateTime("{$year}-8-11"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1966;
        $this->assertEquals(new \DateTime("{$year}-9-15"), $type->getDate($year));
        $year = 2002;
        $this->assertEquals(new \DateTime("{$year}-9-15"), $type->getDate($year));
        // 2003年から9月の第3月曜日。
        $year = 2003;
        $this->assertEquals(new \DateTime("{$year}-9-15"), $type->getDate($year));
        $year = 2010;
        $this->assertEquals(new \DateTime("{$year}-9-20"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1948;
        $this->assertEquals(new \DateTime("{$year}-9-23"), $type->getDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-9-23"), $type->getDate($year));

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
        $this->assertNull($type->getDate($year));

        $year = 1966;
        $this->assertEquals(new \DateTime("{$year}-10-10"), $type->getDate($year));
        $year = 1999;
        $this->assertEquals(new \DateTime("{$year}-10-10"), $type->getDate($year));
        // 2000年から10月の第2月曜日。
        $year = 2000;
        $this->assertEquals(new \DateTime("{$year}-10-9"), $type->getDate($year));
        $year = 2019;
        $this->assertEquals(new \DateTime("{$year}-10-14"), $type->getDate($year));

        // 2020年からスポーツの日に改名。
        $year = 2020;
        $this->assertNull($type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 2020;
        $this->assertEquals(new \DateTime("{$year}-7-24"), $type->getDate($year));
        $year = 2021;
        $this->assertEquals(new \DateTime("{$year}-10-11"), $type->getDate($year));
        $year = 2030;
        $this->assertEquals(new \DateTime("{$year}-10-14"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1948;
        $this->assertEquals(new \DateTime("{$year}-11-3"), $type->getDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-11-3"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1948;
        $this->assertEquals(new \DateTime("{$year}-11-23"), $type->getDate($year));
        $year = 2001;
        $this->assertEquals(new \DateTime("{$year}-11-23"), $type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 1949;
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->getDate($year));
        $year = 1988;
        $this->assertEquals(new \DateTime("{$year}-4-29"), $type->getDate($year));
        // 1989年から12月23日
        $year = 1989;
        $this->assertEquals(new \DateTime("{$year}-12-23"), $type->getDate($year));
        $year = 2018;
        $this->assertEquals(new \DateTime("{$year}-12-23"), $type->getDate($year));
        // 2019年は祝日無し
        $year = 2019;
        $this->assertNull($type->getDate($year));


        // 2020年以降は2月23日
        $year = 2020;
        $this->assertEquals(new \DateTime("{$year}-2-23"), $type->getDate($year));
        $year = 2030;
        $this->assertEquals(new \DateTime("{$year}-2-23"), $type->getDate($year));
    }

    /**
     * 皇太子明仁親王の結婚の儀
     */
    public function 皇太子明仁親王の結婚の儀()
    {
        $type = new Koutaisiakihitosinnounokekonnogi();
        $this->assertEquals('皇太子明仁親王の結婚の儀', $type->getName());

        $year = 1958;
        $this->assertNull($type->getDate($year));

        $year = 1959;
        $this->assertEquals(new \DateTime("{$year}-4-10"), $type->getDate($year));

        $year = 1960;
        $this->assertNull($type->getDate($year));
    }

    /**
     * 皇太子明仁親王の結婚の儀
     */
    public function 皇太子徳仁親王の結婚の儀()
    {
        $type = new Koutaisinaruhitosinnounokekonnogi();
        $this->assertEquals('皇太子徳仁親王の結婚の儀', $type->getName());

        $year = 1992;
        $this->assertNull($type->getDate($year));

        $year = 1993;
        $this->assertEquals(new \DateTime("{$year}-6-9"), $type->getDate($year));

        $year = 1994;
        $this->assertNull($type->getDate($year));

    }

    /**
     * 昭和天皇の大喪の礼
     */
    public function 昭和天皇の大喪の礼()
    {
        $type = new Koutaisinaruhitosinnounokekonnogi();
        $this->assertEquals('昭和天皇の大喪の礼', $type->getName());

        $year = 1988;
        $this->assertNull($type->getDate($year));

        $year = 1989;
        $this->assertEquals(new \DateTime("{$year}-2-24"), $type->getDate($year));

        $year = 1990;
        $this->assertNull($type->getDate($year));
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
        $this->assertNull($type->getDate($year));

        $year = 2019;
        $this->assertEquals(new \DateTime("{$year}-5-1"), $type->getDate($year));

        $year = 2020;
        $this->assertNull($type->getDate($year));

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
        $this->assertNull($type->getDate($year));

        $year = 1990;
        $this->assertEquals(new \DateTime("{$year}-11-12"), $type->getDate($year));

        $year = 1991;
        $this->assertNull($type->getDate($year));

        $year = 2018;
        $this->assertNull($type->getDate($year));

        $year = 2019;
        $this->assertEquals(new \DateTime("{$year}-10-22"), $type->getDate($year));

        $year = 2020;
        $this->assertNull($type->getDate($year));
    }

}
