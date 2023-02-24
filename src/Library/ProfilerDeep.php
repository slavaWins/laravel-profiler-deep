<?php


namespace ProfilerDeep\Library;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProfilerDeep
{

    public $timeCpuAmount = 0;

    private $timeStart = 0;
    private $enabled = false;
    public $list = [];


    public static function textCenter($txt, $len)
    {

        $l = mb_strlen($txt);
        if (!($l & 1)) {
            $txt .= ' ';
        }

        $p = $len - $l;

        if ($p <= 0) return $txt;

        $p = ceil($p / 2);


        $pl = str_repeat(' ', $p);
        $res = $pl . $txt . $pl;

        $resLen = mb_strlen($res);
        if ($len > $resLen) $res .= str_repeat('B', $len - $resLen);

        $lenDelete = $resLen - $len;
        if ($lenDelete > 0) {
            $res = mb_substr($res, 0, $resLen-1);
            if($lenDelete>1) {
             $res = mb_substr($res, 1);
            }
        }


        return $res;
    }

    public static function ConsoleTableDraw($data)
    {
        $columLens = [];
        foreach ($data as $row) {
            foreach ($row as $K => $val) {
                $columLens[$K] = max(strlen(($val)), ($columLens[$K] ?? 0 )+2 ) ;
            }
        }



        $render = '';

        $lenLine = 0;
        foreach ($data as $rowId => $row) {

            foreach ($row as $K => $val) {
                $render .= "|" . self::textCenter($val, $columLens[$K]);
            }
            $render .= "|";

            if ($lenLine == 0) {
                $lenLine = mb_strlen($render) - 2;
            }
            $render .= "\n";

            if ($rowId == 0) $render .= "+" . str_repeat("-", $lenLine) . "+\n";

        }


        $render = "\n+" . str_repeat("-", $lenLine) . "+\n" . $render;
        $render = $render . "+" . str_repeat("-", $lenLine) . "+\n";
        return $render;
    }

    private $listner;

    public function Start()
    {
        $this->timeStart = microtime(true);

        $this->enabled = true;

        if(!$this->listner) {
            DB::listen(function ($query) {
                if (!$this->enabled) return;
                $this->list[] = [
                    'q' => $query->sql,
                    'time' => $query->time,
                ];
            });

            $this->listner = true;
        }
    }


    public static function GetQueryShemeUse($Q)
    {

        $res = [];

        $Q = preg_replace('/[\s]{2,}/', ' ', $Q);
        $list = explode('`', $Q);

        foreach ($list as $V) {
            if (empty($V)) continue;
            if (strpos($V, " ") > -1) continue;

            $res[$V] = $V;
        }
        return $res;
    }

    public static function GetQueryTable($Q)
    {

        if ($Q == 'SET NAMES utf8mb4') return '';

        $Q = preg_replace('/[\s]{2,}/', ' ', $Q);
        $list = explode('`', $Q);

        foreach ($list as $V) {
            if (empty($V)) continue;
            if (strpos($V, " ") > -1) continue;
            if ($V == 'id') continue;
            dump($V);
        }
        dd("X");
        foreach ($Q as $V) {
            if (strpos($V, 'tb_') === 0) {

                if (strpos($V, '.') > -1) $V = explode('.', $V)[0];

                return $V;
                break;
            }
        }

        return '';
    }


    public static function GetQueryType($Q)
    {
        $Q = strtolower($Q);
        if (strpos($Q, '.join ') > -1) return 'join';
        if (strpos($Q, 'select ') > -1) return 'select';
        if (strpos($Q, 'update ') > -1) return 'update';
        if (strpos($Q, 'delete ') > -1) return 'delete';
        if (strpos($Q, 'insert into ') > -1) return 'insert';
        if (strpos($Q, '.join') > -1) return 'join';
        if (strpos($Q, 'join') > -1) return 'join';
        if (strpos($Q, 'alter table') > -1) return 'createTable';
        if (strpos($Q, 'create table if') > -1) return 'createTable';

        return 'xz';
    }


    public static function ParseQ($q)
    {
        $elements = self::GetQueryShemeUse($q);

        $table = array_shift($elements);

        $result = [
            'table' => $table,
            'type' => self::GetQueryType($q),
        ];

        return $result;
    }

    public function RenderTable($title = "")
    {
        echo $this->Anlz($title)['table'];
    }

    public function Anlz($title = "")
    {
        $anlzByTablesType = [];
        $anlzByTables = [];
        $anlzByType = [];

        $response = [];

        $response['timeCpu'] =$this->timeCpuAmount;
        $response['time'] = 0;
        foreach ($this->list as $Q) {
            $parse = self::ParseQ($Q['q']);

            $response['time'] += $Q['time'];
            if (!isset($anlzByTables[$parse['table']])) $anlzByTables[$parse['table']] = ['count' => 0, 'time' => 0];
            $anlzByTables[$parse['table']]['time'] += $Q['time'];
            $anlzByTables[$parse['table']]['count'] += 1;

            if (!isset($anlzByType[$parse['type']])) $anlzByType[$parse['type']] = ['count' => 0, 'time' => 0];
            $anlzByType[$parse['type']]['time'] += $Q['time'];
            $anlzByType[$parse['type']]['count'] += 1;

            $key = $parse['type'] . ' ' . $parse['table'];
            if (!isset($anlzByTablesType[$key])) $anlzByTablesType[$key] = ['count' => 0, 'time' => 0];
            $anlzByTablesType[$key]['time'] += $Q['time'];
            $anlzByTablesType[$key]['count'] += 1;
        }

        arsort($anlzByTablesType);
        arsort($anlzByType);
        arsort($anlzByTables);


        $response['by'] = [];
        $response['by']['table'] = $anlzByTables;
        $response['by']['type'] = $anlzByType;
        $response['by']['tableType'] = $anlzByTablesType;


        $console = '**** PROFILLER RESULT: ' . $title . ' ****';
        $console .= "\nОбщее время CPU: " . round( $response['timeCpu'], 4) . ' sec.   ';
        $console .= "\nОбщее время SQL: " . round($response['time'] / 1000, 4) . ' sec.  or ' . round($response['time'], 2) . ' msec.  ';


        $console .= "\nЗапросы по таблицам";
        $res = [['Таблица', 'Колв', 'Время']];
        foreach ($anlzByTables as $K => $V) $res[] = [$K, $V['count'], round($V['time'], 2) . ' ms'];
        $console .= " " . self::ConsoleTableDraw($res);


        $console .= "\n\nЗапросы по типу и таблице";
        $res = [['Таблица', 'Колв', 'Время']];
        foreach ($anlzByTablesType as $K => $V) $res[] = [$K, $V['count'], round($V['time'], 2) . ' ms'];
        $console .= " " . self::ConsoleTableDraw($res);


        $console .= "\n\nЗапросы по типу";
        $res = [['Таблица', 'Колв', 'Время']];
        foreach ($anlzByType as $K => $V) $res[] = [$K, $V['count'], round($V['time'], 2) . ' ms'];
        $console .= " " . self::ConsoleTableDraw($res);


        $response['table'] = $console;

        return $response;
    }

    public function Stop()
    {
        if (!$this->enabled) return;

        $res = microtime(true) - $this->timeStart;

        $this->timeCpuAmount+=$res;

        $this->timeStart = null;

        $this->enabled = false;
    }



}
