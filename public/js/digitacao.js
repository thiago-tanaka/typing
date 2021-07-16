function get(id) {
    return document.getElementById(id);
}

const digitacao = {
    // ATTRIBUTOS
    atual: {
        linha: 1,
        posicao: 0
    },
    velocidade: 0,
    segundos: 1,
    start_velocicade: 0,
    letras_digitadas_total: 0,
    letras_digitadas_certas: 0,
    letras_digitadas_erradas: 0,
    precisao: 100,
    pisca: 0,
    acabou_texto: false,
    get letra_atual() {
        return this.atual.linha <= 4
            ? texto[this.atual.linha].substr(this.atual.posicao, 1)
            : "";
    },

    // FUNÇÕES
    insereTexto: function() {
        function insere_under_line(linha) {
            let first_txt = texto[linha].substr(0, digitacao.atual.posicao);
            let central_txt = texto[linha].substr(digitacao.atual.posicao, 1);
            let last_txt = texto[linha].substr(digitacao.atual.posicao + 1);

            return (
                first_txt +
                '<span id="blink" class="blink" >' +
                central_txt +
                "</span>" +
                last_txt
            );
        }

        get("texto1").innerHTML =
            this.atual.linha == 1 ? insere_under_line(1) : texto[1];
        get("texto2").innerHTML =
            this.atual.linha == 2 ? insere_under_line(2) : texto[2];
        get("texto3").innerHTML =
            this.atual.linha == 3 ? insere_under_line(3) : texto[3];
        get("texto4").innerHTML =
            this.atual.linha == 4 ? insere_under_line(4) : texto[4];
    },

    // Altera atributos quando chegar na ultima letra da linha em questão
    verificarAcabouLinha: function() {
        if (this.atual.posicao == texto[this.atual.linha].length) {
            this.atual.posicao = 0;
            this.atual.linha++;
        }
    },

    // seta o atributo "acabou_texto" para true quando chegar ao fim do texto
    verificarAcabouTexto: function() {
        if (
            this.atual.linha == 4 &&
            this.atual.posicao + 1 == texto[4].length
        ) {
            this.acabou_texto = true;
        } else {
            this.acabou_texto = false;
        }
    },

    // Para o setInterval()
    kill_interval: function() {
        if (this.acabou_texto == true) {
            clearInterval(blink_interval);
            clearInterval(velocidade_interval);
        }
    },

    qual_dedo_usar: function() {
        var dedo1 = ["a", "q", "z", "ã"];
        var dedo2 = ["s", "w", "x"];
        var dedo3 = ["d", "e", "c", "é"];
        var dedo4 = ["f", "r", "v", "t", "g", "b"];
        var dedo5 = [" "];
        var dedo6 = [" "];
        var dedo7 = ["h", "y", "n", "j", "u", "m"];
        var dedo8 = ["k", "i", ","];
        var dedo9 = ["l", "o", ".", "ó"];
        var dedo10 = ["ç", "p", ";", "ã", "é", "ó"];

        // Adiciona bolinha na mão esquerda
        if (dedo1.indexOf(this.letra_atual) !== -1) {
            get("bolinha").className = "bol-1";
        } else if (dedo2.indexOf(this.letra_atual) !== -1) {
            get("bolinha").className = "bol-2";
        } else if (dedo3.indexOf(this.letra_atual) !== -1) {
            get("bolinha").className = "bol-3";
        } else if (dedo4.indexOf(this.letra_atual) !== -1) {
            get("bolinha").className = "bol-4";
        } else if (dedo5.indexOf(this.letra_atual) !== -1) {
            get("bolinha").className = "bol-5";
        } else {
            get("bolinha").className = "hidden";
        }

        // Adiciona bolinha na mão direita
        if (dedo6.indexOf(this.letra_atual) !== -1) {
            get("bolinha2").className = "bol-6";
        } else if (dedo7.indexOf(this.letra_atual) !== -1) {
            get("bolinha2").className = "bol-7";
        } else if (dedo8.indexOf(this.letra_atual) !== -1) {
            get("bolinha2").className = "bol-8";
        } else if (dedo9.indexOf(this.letra_atual) !== -1) {
            if(this.letra_atual === 'ó'){
                get("bolinha3").className = "bol-10";
            }
            get("bolinha2").className = "bol-9";
        } else if (dedo10.indexOf(this.letra_atual) !== -1) {
            get("bolinha2").className = "bol-10";
        } else {
            get("bolinha2").className = "hidden";
            get("bolinha3").className = "hidden";
        }
    },

    add_fundo_verde: function() {
        if (!this.acabou_texto) {
            if (this.letra_atual == "ã") {
                get("a").classList.add("letra_certa");
                get("~").classList.add("letra_certa");
            } else if (this.letra_atual == "é") {
                get("e").classList.add("letra_certa");
                get("´").classList.add("letra_certa");
            } else if (this.letra_atual == "ó") {
                get("o").classList.add("letra_certa");
                get("´").classList.add("letra_certa");
            }
            else if (this.letra_atual == " ") {
                get("space").classList.add("letra_certa");
            } else {
                get(this.letra_atual).classList.add("letra_certa");
            }
        }
    },

    digitou_errado: function(letra_digitada) {
        if (!this.acabou_texto) {
            if (letra_digitada == " ") {
                get("space").classList.add("letra_errada");
            } else {
                get(letra_digitada).classList.add("letra_errada");
            }
        }
    },

    remove_fundo_certo: function() {
        if (this.letra_atual == " ") {
            get("space").classList.remove("letra_certa");
        } else if (this.letra_atual == "ã") {
            get("a").classList.remove("letra_certa");
            get("~").classList.remove("letra_certa");
        } else if (this.letra_atual == "é") {
            get("e").classList.remove("letra_certa");
            get("´").classList.remove("letra_certa");
        } else if (this.letra_atual == "ó") {
            get("o").classList.remove("letra_certa");
            get("´").classList.remove("letra_certa");
        }
        else {
            get(this.letra_atual).classList.remove("letra_certa");
        }
    },

    calcularPrecisao: function() {
        var precisao =
            this.letras_digitadas_certas / this.letras_digitadas_total;
        get("precisao").innerHTML = Math.round(precisao * 100) + "%";
        // console.log(Math.round(precisao * 100));
        return Math.round(precisao * 100);
    },

    calcularVelocidade: function() {
        if (this.start_velocicade === 0 && this.letras_digitadas_certas > 0) {
            velocidade_interval = setInterval(function() {
                digitacao.velocidade = Math.round(
                    (digitacao.letras_digitadas_certas * 60) /
                    digitacao.segundos
                );

                get("velocidade").innerHTML = digitacao.velocidade;

                digitacao.segundos++;
            }, 1000);
            this.start_velocicade++;
        }
    },

    salvarNoBanco: function() {
        if (digitacao.acabou_texto && get("formulario")) {
            var velocidade = digitacao.velocidade;
            var precisao = digitacao.calcularPrecisao();

            get("licao").value = velocidade + " / " + precisao + "%";
            get("formulario").submit();
        }
    }
};

window.onkeypress = function(e) {
    e.preventDefault();

    digitacao.letras_digitadas_total++;

    if (String.fromCharCode(e.charCode) == digitacao.letra_atual) {
        digitacao.letras_digitadas_certas++;
        digitacao.remove_fundo_certo();
        digitacao.verificarAcabouTexto();
        digitacao.kill_interval();
        digitacao.atual.posicao++;
        digitacao.verificarAcabouLinha();
        digitacao.insereTexto();
    } else {
        digitacao.letras_digitadas_erradas++;
        digitacao.digitou_errado(String.fromCharCode(e.charCode));

        // Remove todas class "letra_errada"
        window.onkeyup = function(e) {
            var elementos = document.querySelectorAll(".letra_errada");
            for (var i = 0; i < elementos.length; i++) {
                elementos[i].classList.remove("letra_errada");
            }
        };
    }
    digitacao.add_fundo_verde();
    digitacao.qual_dedo_usar();
    digitacao.calcularPrecisao();
    digitacao.calcularVelocidade();
    digitacao.salvarNoBanco();
};

window.onload = function() {
    digitacao.add_fundo_verde();
    digitacao.qual_dedo_usar();

    blink_interval = setInterval(function() {
        if (digitacao.pisca % 2 == 0) {
            get("blink").className = "blink2";
        } else {
            get("blink").className = "blink";
        }
        digitacao.pisca++;
    }, 500);
};
