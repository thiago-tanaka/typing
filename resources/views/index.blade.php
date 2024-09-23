@extends('layouts.app')

@section('content')

    <div class="row  d-flex justify-content-center  mb-3 ">
        <div class="col-2 text-center">
            <div class="row">
                <div class="col">
                    Speed
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
                @foreach($pontuacoes as $pontuacao)
                    <div class="col-2 div-licoes2">@auth {!! $pontuacao !!} @endauth</div>
                @endforeach
            </div>
        </div>

        <div class="col-2 text-center">
            <div class="row">
                <div class="col">
                    Accuracy
                </div>
            </div>
            <div class="row">
                <div id="precisao" class="col precisao ">
                    100%
                </div>
            </div>
        </div>
    </div>
    <div class="row  mb-3 d-flex justify-content-center">
        <div class="col-1">
        </div>
        <div class="col-9 border rounded bg-white text-center" style="box-shadow: 0 5px 10px #9a9a9a;">
            <div class="texto_principal" id="texto1"></div>
            <div class="texto_principal" id="texto2"></div>
            <div class="texto_principal" id="texto3"></div>
            <div class="texto_principal" id="texto4"></div>
        </div>
        <div class="col-1">

            @foreach($pontuacoes_chart as $p)
                <div class="s text-nowrap" style="font-size:0.8rem">
                    {!! $p !!}
                </div>
            @endforeach
        </div>
    </div>

    <div class="row p-1">
        <div class="col-xl-2  p-0 m-0 ">
            <img src="{{ asset('img/mao_esquerda.png') }}" width="170px" alt="">
            <img src="{{ asset('img/bolinha.png') }}" width="15px" id="bolinha" class="hidden" alt="">
        </div>

        <div style="opacity: 0" class="col-xl-8 p-0 m-0 d-none d-md-block ">@include('keyboard_html')

        </div>

        <div class="col-xl-2 p-0 m-0 text-right ">
            <img class=" " src="{{ asset('img/mao_direita.png') }}" width="170px" alt="">
            <img src="{{ asset('img/bolinha.png') }}" width="15px" id="bolinha2" class="hidden" alt="">
            <img src="{{ asset('img/bolinha.png') }}" width="15px" id="bolinha3" class="hidden" alt="">
        </div>

    </div>

    @auth
        <form id="formulario" method="post" action="/registra/{{ $unidade }}/{{ $licao }}">
            @csrf
            <input type="hidden" name="id" id="{{ auth()->id() }}">
            <input type="hidden" name="licao_velocidade" id="licao_velocidade" value="">
            <input type="hidden" name="licao_precisao" id="licao_precisao" value="">

        </form>
    @endauth
@endsection

@section('script')

    <script>
        var texto = {!! json_encode($texto) !!}
    </script>
    <script src="{{ asset('js/digitacao.js') }}"></script>
    <script>
        digitacao.insereTexto();
    </script>

@endsection
