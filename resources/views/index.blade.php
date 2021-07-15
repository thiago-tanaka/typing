@extends('layouts.app')

@section('content')

    <div class="row  d-flex justify-content-center  mb-3 ">
        <div class="col-2 text-center">
            <div class="row">
                <div class="col">
                    Velocidade
                </div>
            </div>
            <div class="row">
                <div id="velocidade" class="col precisao ">
                    0
                </div>
            </div>
        </div>
        <div class="col-8  ">
            <div class="row d-flex justify-content-center mt-2">
                <div class="col-2 div-licoes @if ($licao == 1 ) atual @endif "><a href=" {{ url("/$unidade") }}/1">1</a>
                </div>
                <div class="col-2 div-licoes @if ($licao == 2 ) atual @endif "><a href=" {{ url("/$unidade") }}/2">2</a>
                </div>
                <div class="col-2 div-licoes @if ($licao == 3 ) atual @endif "><a href=" {{ url("/$unidade") }}/3">3</a>
                </div>
                <div class="col-2 div-licoes @if ($licao == 4 ) atual @endif "><a href=" {{ url("/$unidade") }}/4">4</a>
                </div>
                <div class="col-2 div-licoes @if ($licao == 5 ) atual @endif "><a href=" {{ url("/$unidade") }}/5">5</a>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-2 div-licoes2">@auth {{  Auth::user()["licao_".$unidade."_1"] }} @endauth</div>
                <div class="col-2 div-licoes2">@auth {{  Auth::user()["licao_".$unidade."_2"] }}@endauth</div>
                <div class="col-2 div-licoes2">@auth {{  Auth::user()["licao_".$unidade."_3"] }}@endauth</div>
                <div class="col-2 div-licoes2">@auth {{  Auth::user()["licao_".$unidade."_4"] }}@endauth</div>
                <div class="col-2 div-licoes2">@auth {{  Auth::user()["licao_".$unidade."_5"] }}@endauth</div>
            </div>
        </div>

        <div class="col-2 text-center">
            <div class="row">
                <div class="col">
                    Precisão
                </div>
            </div>
            <div class="row">
                <div id="precisao" class="col precisao ">
                    100%
                </div>
            </div>
        </div>
    </div>
    <div class="row border mb-3 div-texto">
        <div class="col ">
            <div class="texto_principal" id="texto1"></div>
            <div class="texto_principal" id="texto2"></div>
            <div class="texto_principal" id="texto3"></div>
            <div class="texto_principal" id="texto4"></div>
        </div>

    </div>

    <div class="row p-1">
        <div class="col-xl-2  p-0 m-0 ">
            <img src="{{ asset('img/mao_esquerda.png') }}" width="170px" alt="">
            <img src="{{ asset('img/bolinha.png') }}" width="15px" id="bolinha" class="hidden" alt="">
        </div>

        <div class="col-xl-8 p-0 m-0 d-none d-md-block ">@include('keyboard_html')

        </div>

        <div class="col-xl-2 p-0 m-0 text-right ">
            <img class=" " src="{{ asset('img/mao_direita.png') }}" width="170px" alt="">
            <img src="{{ asset('img/bolinha.png') }}" width="15px" id="bolinha2" class="hidden" alt="">
        </div>

    </div>

    @auth
        <form id="formulario" method="post" action="/registra/{{ $unidade }}/{{ $licao }}">
            @csrf
            <input type="hidden" name="id" id="{{ auth()->id() }}">
            <input type="hidden" name="licao" id="licao" value="">

        </form>
    @endauth


    <script src="{{ asset('js/digitacao.js') }}"></script>
    <script>
        digitacao.insereTexto();
    </script>

@endsection

{{-- $texto está vindo de DigitacaoController@index --}}
<script>
    var texto = {!! $texto !!}
</script>
