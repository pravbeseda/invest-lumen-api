<?php

namespace App\Http\Controllers;

class McxController extends Controller
{
    private $shares = ['TQTF', 'TQBR', 'SNDX', 'TQIF'];

    public function searchStock(string $ticker)
    {
        $boardId = $this->getBoard($ticker);

        return $this->getInfo($ticker, $boardId);
    }

    public function getLastPrice($stock)
    {
        $type = (in_array($stock->boardId, $this->shares)) ? 'shares' : 'bonds';
        $url = "https://iss.moex.com/iss/engines/stock/markets/$type/boards/$stock->boardId/securities.xml?iss.meta=on&iss.only=marketdata&marketdata.columns=SECID,LAST";
        $xml = simplexml_load_file($url);
        $attr = $xml->xpath("//row[@SECID='$stock->ticker']")[0]->attributes();

        return (string) $attr->LAST;
    }

    private function getBoard(string $ticker)
    {
        $url = "https://iss.moex.com/iss/securities/$ticker.xml?iss.meta=off&iss.only=boards&boards.columns=secid,is_primary,boardid";
        $xml = simplexml_load_file($url);

        return (string) $xml->xpath('/document/data/rows/row[@is_primary="1"]')[0]->attributes()->boardid;
    }

    private function getInfo(string $ticker, string $boardId)
    {
        $type = (in_array($boardId, $this->shares)) ? 'shares' : 'bonds';
        $url = "https://iss.moex.com/iss/engines/stock/markets/$type/boards/$boardId/securities.xml?iss.meta=off&iss.only=securities";
        $xml = simplexml_load_file($url);
        $attr = $xml->xpath("//row[@SECID='$ticker']")[0]->attributes();
        $currencyId = (string) $attr->CURRENCYID;
        $type = (string) $attr->INSTRID;

        return [
            'ticker' => (string) $attr->SECID,
            'boardId' => (string) $boardId,
            'name' => (string) $attr->SECNAME,
            'currency' => ($currencyId == 'SUR') ? 'RUB' : $currencyId,
            'isin' => (string) $attr->ISIN,
            'type' => ($type == 'IFTF') ? 'Etf' : 'Stock',
            'lastPrice' => (string) $attr->PREVADMITTEDQUOTE,
            'driver' => 'MCX',
        ];
    }
}
