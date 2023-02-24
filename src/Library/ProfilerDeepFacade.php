<?php


namespace ProfilerDeep\Library;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProfilerDeepFacade
{


    private static $currentPart = null;

    /** @var ProfilerDeep[] $parts */
    public static $parts = [];
    public static $partsLoops = [];

    public static function ResultPartsSqlFromAllParts()
    {

        if (!env("PROFILER_DEEP_ENABLE")) return "Not property in env PROFILER_DEEP_ENABLE=true";
        self::Stop();

        $info = '';


        $deep = new ProfilerDeep();

        foreach (self:: $parts as $name => $part) {
            $deep->list = array_merge($deep->list, $part->list);
            $deep->timeCpuAmount += $part->timeCpuAmount;
        }

        $data = $deep->Anlz($name);
        $info .= "\n\n" . $data['table'];

        return $info;
    }

    public static function ResultPartsSql()
    {

        if (!env("PROFILER_DEEP_ENABLE")) return "Not property in env PROFILER_DEEP_ENABLE=true";
        self::Stop();

        $info = '';

        foreach (self:: $parts as $name => $part) {
            $data = $part->Anlz($name);
            $info .= "\n\n" . $data['table'];
        }


        return $info;
    }


    public static function ResultParts()
    {

        if (!env("PROFILER_DEEP_ENABLE")) return "Not property in env PROFILER_DEEP_ENABLE=true";
        self::Stop();

        $info = '';

        $table = [];
        $table[] = [
            'Time CPU',
            'Time SQL',
            'Repeats',
            'Part name',
        ];

        foreach (self:: $parts as $name => $part) {

            $data = $part->Anlz($name);
            $table[] = [
                round($data['timeCpu'], 2) . ' sec.',
                round($data['time'] / 1000, 4) . ' sec.',
                self::$partsLoops[$name],
                $name,
            ];
        }

        $info = ProfilerDeep::ConsoleTableDraw($table);

        return $info;
    }



    public static function Clear(){
        self::$currentPart = null;
        self:: $parts = [];
    }

    public static function Stop()
    {
        if (!self::$currentPart) return;


        self:: $parts[self::$currentPart]->Stop();


        self::$currentPart = null;
    }


    public static function AddPart($name = '')
    {
        if (!env("PROFILER_DEEP_ENABLE")) return;

        $fromClass = str_replace('.php', '', basename(debug_backtrace()[0]['file']));
        $fromLine = basename(debug_backtrace()[0]['line']);

        $name .= '  ' . $fromClass . ':' . $fromLine;

        if (self::$currentPart) {
            self::Stop();
        }

        self::$currentPart = $name;

        if (!isset(self:: $parts[self::$currentPart])) {
            self:: $parts[self::$currentPart] = new ProfilerDeep();
        }
        self:: $parts[self::$currentPart]->Start();

        if (isset(self::$partsLoops[$name])) {
            self::$partsLoops[$name]++;
        } else {
            self::$partsLoops[$name] = 1;
        }


    }
}
