<?php

namespace App\Http\Controllers;

use Vedmant\FeedReader\Facades\FeedReader;
use App\Models\Valuta;
use Illuminate\Http\Request;

class ValutaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $valutas = Valuta::all();
        $current_data = date('d/m/Y');
        $url = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req='.$current_data;
        $f = FeedReader::read($url);
        $datas = [];
        foreach ($f->data['child']['']['ValCurs'][0]['child']['']['Valute'] as $key => $value) {
            if ($value['child']['']['CharCode'][0]['data'] == 'USD' || $value['child']['']['CharCode'][0]['data'] == 'EUR') {
                $datas[] = $value;
            }
        }
        $all[0][] = $datas[0]['child']['']['CharCode'][0]['data'];
        $all[0][] = $datas[0]['child']['']['NumCode'][0]['data'];
        $all[1][] = $datas[1]['child']['']['CharCode'][0]['data'];
        $all[1][] = $datas[1]['child']['']['NumCode'][0]['data'];

        return view('valutas.index', compact('valutas', 'all', 'datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('valutas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'valuta' => 'required',
                'code' => 'required',
            ]
        );

        Valuta::create($request->all());
        return redirect('/valutas')->with('success', 'Valuta saved.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Valuta  $valuta
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $valutas = Valuta::all();
        $current_data = date('d/m/Y');
        $url = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req='.$current_data;
        $f = FeedReader::read($url);
        if (isset($valutas) && count($valutas) == 0) {
            $datas = [];
            foreach ($f->data['child']['']['ValCurs'][0]['child']['']['Valute'] as $key => $value) {
                if ($value['child']['']['CharCode'][0]['data'] == 'USD' || $value['child']['']['CharCode'][0]['data'] == 'EUR') {
                    $datas[] = $value;
                }
            }
            $all[0]['valuta'] = $datas[0]['child']['']['CharCode'][0]['data'];
            $all[0]['code'] = $datas[0]['child']['']['NumCode'][0]['data'];
            $all[0]['price'] = $datas[0]['child']['']['Value'][0]['data'];
            $all[1]['valuta'] = $datas[1]['child']['']['CharCode'][0]['data'];
            $all[1]['code'] = $datas[1]['child']['']['NumCode'][0]['data'];
            $all[1]['price'] = $datas[1]['child']['']['Value'][0]['data'];
            $flag = true;
            return view('index', compact('all', 'flag'));
        } else {
            $datas = [];
            foreach ($f->data['child']['']['ValCurs'][0]['child']['']['Valute'] as $key => $value) {
                foreach ($valutas as $key => $valuta) {
                    if ($value['child']['']['CharCode'][0]['data'] == $valuta->valuta) {
                        $datas[] = $value;
                        $datas[$key]['procent'] = $valuta->procent;
                    }
                }
            }
            foreach ($datas as $key => $data) {
                $all[$key]['valuta'] = $data['child']['']['CharCode'][0]['data'];
                $all[$key]['code'] = $data['child']['']['NumCode'][0]['data'];
                $all[$key]['price'] = $data['child']['']['Value'][0]['data'];
                $all[$key]['procent'] = $data['procent'];
            }
            $flag = false;
            return view('index', compact('all', 'flag'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Valuta  $valuta
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $valuta = Valuta::findOrFail($id);
        return view('valutas.edit', compact('valuta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Valuta  $valuta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'valuta' => 'required',
                'code' => 'required',
            ]
        );

        $valuta = Valuta::find($id);
        $valuta->update($request->all());

        return redirect('/valutas')->with('success', 'Valuta updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Valuta  $valuta
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $valuta = Valuta::find($id);
        $valuta->delete();

        return redirect('/valutas')->with('success', 'Valuta removed.');
    }
}
