var usuarioSeleccionado = '';
var datosRecipientes = '';
var id = '';
var datos2 = [];

window.fn = {};
var db = window.openDatabase("Favs", "1.0", "Favoritos", 1024 * 1024 * 5);

window.fn.open = function () {
    var menu = document.getElementById('menu');
    menu.open();
};
window.fn.load = function (page) {
    var content = document.getElementById('content');
    var menu = document.getElementById('menu');
    content.load(page)
        .then(menu.close.bind(menu));
};

document.addEventListener("init", inicializarPagina);

function inicializarPagina(evt) {
    var destino = evt.target.id;
    switch (destino) {
        case "home":
            $("#btnIngresar").click(cambioPag);
            $("#btnRegistrar").click(registrar);
            $(".tabbar").hide();
            break;

        case "registrar":
            $("#btnRegistro").click(registrarNuevo);

            break;

        case "alimentos":
            $(".mascara1").hide();
            $(".mascara1a").hide();
            cargarAlimentos2();

            $("#btnCancelar2").click(cancelarEditar);
            $("#btnConfirmar2").click(editarRecipiente);

            break;

        case "recetas":
            cargarRecetas();

            break;

        case "dashboard":

            $(".dialog-mask").hide();
            $(".dialog").hide();
            cargarAlimentos();
            $("#btnAgregar").click(agregarRecipiente);
            $("#btnConfirmar").click(confirmarRecipiente);
            $("#btnCancelar").click(cancelarRecipiente);

            break;

        case "favoritos":

            break;

        case "perfil":
            $("#btnAgregarReceta").click(agregarReceta);
            break;

        case "ampliacion":
            break;

        case "agregar":

    }
}

function cambioPag() {
    var user = $("#txtUsuarioDos").val();
    var pass = $("#txtContrasena").val();
    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "GET",
        dataType: "JSON",
        data: {
            accion: "ingresar",
            userData: user,
            passData: pass
        },
        success: cargarDashboard,
        error: mensajeError
    });

}

function cargarDashboard(datosUsuario) {
    if (usuarioSeleccionado == "") {
        usuarioSeleccionado = datosUsuario.id
    }
    document.getElementById('content').load("dashboard.html");
}

function cargarAlimentos() {

    $(".tabbar").show();

    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "GET",
        dataType: "JSON",
        data: {
            accion: "datosRecipientes",
            datoElegido: usuarioSeleccionado
        },
        success: mostrarDatos
    });
}

function cargarAlimentos2() {

    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "GET",
        dataType: "JSON",
        data: {
            accion: "datosRecipientes",
            datoElegido: usuarioSeleccionado
        },
        success: mostrarDatosAlimentos
    });
}

function mensajeError() {
    $("#mensaje").text("Datos incorrectos");
}

function registrar() {
    document.getElementById('content').load("registrar.html");
}

function registrarNuevo() {

    var usuario = $("#txtUsuario").val();
    var contrasenia = $("#txtContrasenia").val();

    var fd = new FormData();
    fd.append('usuario', usuario);
    fd.append('contra', contrasenia);

    if (usuario === "" || contrasenia === "") {
        $("#mensaje").text("Debes completar todos los campos");
    } else {
        $.ajax({
            url: "http://localhost/eBalanceBack/api/upload.php",
            type: "POST",
            dataType: "JSON",
            data: fd,
            contentType: false,
            processData: false,
            success: registroIngresado
        });
        document.getElementById('content').load("home.html");
    }
}

function registroIngresado() {
    alert("Registro realizado correctamente");
}

function mostrarDatos(datosTodos) {

    datosRecipientes = datosTodos

    for (var i = 0; i < datosTodos.length; i++) {
        var promedio = datosTodos[i].pesoActual * 100
        var promedioFinal = promedio / datosTodos[i].pesoMaximo
        var promedioDecimales = promedioFinal.toFixed(0)

        if (promedioDecimales < 0) {
            promedioDecimales = 0;
        } else if (promedioDecimales > 100) {
            promedioDecimales = 100;
        }

        $(".listaRecipientes").append("<div class='cajaRecipientes'><div class='card'><h2 class='card__title'>" + promedioDecimales + '%' + "</h2><div class='card__content'>" + datosTodos[i].alimento + "</div></div></div>")
    }
    id = $(this).attr("data-id");
}

function mostrarDatosAlimentos(datos2) {
    $(".mascara1").hide();
    $(".mascara1a").hide();
    $(".list").empty();

    datosRecipientes = datos2
    for (var i = 0; i < datos2.length; i++) {
        $(".list").append("<li class='list-item'><div class='list-item__left'><img class='list-item__thumbnail' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAIAAAADnC86AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3wwJCB8v/9zErgAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAAAvSURBVFjD7c0BDQAACAMgtX+KJzWGm4MCdJK6MHVELBaLxWKxWCwWi8VisVj8MV7qBgI2A8rYpgAAAABJRU5ErkJggg=='></div><div class='list-item__center'><div class='list-item__title'>" + datos2[i].alimento + "</div><div class='list-item__subtitle'></div></div><div class='list-item__right'><p>" + datos2[i].pesoActual + "</p><p>" + datos2[i].pesoMaximo + "</p><input type='button' data-id='" + datos2[i].id + "' class='button button--material btnEditar' value='Editar'></div><input type='button' data-id='" + datos2[i].id + "' class='button button--material btnEliminar' value='Eliminar'></div></li>")
    }

    $(".btnEditar").click(mostrarEditar);
    $(".btnEliminar").click(eliminarRecipiente);

}

function agregarRecipiente() {
    $(".dialog-mask").show();
    $(".dialog").show();
}

function confirmarRecipiente() {

    var alimentoSeleccionado = $("#opcionesAlimentos").val();
    var tamanioSeleccionado = $('input[name="r"]:checked').val();
    var pesoMaximoSeleccionado = "";

    if (tamanioSeleccionado == "XL") {
        pesoMaximoSeleccionado = "2000";
    } else if (tamanioSeleccionado == "L") {
        pesoMaximoSeleccionado = "1000";
    } else if (tamanioSeleccionado == "M") {
        pesoMaximoSeleccionado = "500";
    } else {
        pesoMaximoSeleccionado = "100";
    }

    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "POST",
        dataType: "JSON",
        data: {
            idUsuarioData: usuarioSeleccionado,
            tamanioData: tamanioSeleccionado,
            alimentoData: alimentoSeleccionado,
            pesoMaximoData: pesoMaximoSeleccionado,
            accion: "nuevoRecipiente"
        },
        success: recipienteIngresado
    });
}

function recipienteIngresado() {
    document.getElementById('content').load("dashboard.html");
}

function cancelarRecipiente() {
    $(".dialog-mask").hide();
    $(".dialog").hide();
}

function editarRecipiente() {

    var alimentoSeleccionado = $("#opcionesAlimentos").val();
    var tamanioSeleccionado = $('input[name="r"]:checked').val();
    var numero = id;

    if (tamanioSeleccionado == "XL") {
        pesoMaximoSeleccionado = "2000";
    } else if (tamanioSeleccionado == "L") {
        pesoMaximoSeleccionado = "1000";
    } else if (tamanioSeleccionado == "M") {
        pesoMaximoSeleccionado = "500";
    } else {
        pesoMaximoSeleccionado = "100";
    }

    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "PUT",
        dataType: "JSON",
        data: {
            id: numero,
            tamanio: tamanioSeleccionado,
            alimento: alimentoSeleccionado,
            pesoMaximo: pesoMaximoSeleccionado,
            accion: "datosAlimentos"
        },
        success: cargarAlimentos2
    });

}

function cancelarEditar() {
    $(".mascara1").hide();
    $(".mascara1a").hide();
}

function mostrarEditar() {
    id = $(this).attr("data-id");
    $(".mascara1").show();
    $(".mascara1a").show();
}

function eliminarRecipiente() {
    var id = $(this).attr("data-id");
    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "DELETE",
        dataType: "JSON",
        data: {
            idEliminado: id
        },
        success: cargarAlimentos2
    });
}

function alimentosCargados() {
    document.getElementById('content').load("alimentos.html");
}

function cargarRecetas() {

    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "GET",
        dataType: "JSON",
        data: {
            accion: "datosReceta"
        },
        success: mostrarRecetas,
    });
}

function mostrarRecetas(datosRecetas) {
    for (var i = 0; i < datosRecetas.length; i++) {
        $(".listaRecetasRecientes").append("<div class='cajaRecetas'><div class='card' data-id='" + datosRecetas[i].id + "'><img src='" + datosRecetas[i].imgReceta + "'><div class='card__content'>" + datosRecetas[i].nombre + "</div></div></div>")
    }

    for (var x = 0; x < datosRecipientes.length; x++) {
        for (var i = 0; i < datosRecetas.length; i++) {
            if ((datosRecetas[i].ingrediente1 === datosRecipientes[x].alimento) || (datosRecetas[i].ingrediente2 === datosRecipientes[x].alimento) || (datosRecetas[i].ingrediente3 === datosRecipientes[x].alimento)) {
                $(".listaRecetasRelacionadas").append("<div class='cajaRecetas'><div class='card' data-id='" + datosRecetas[i].id + "'><img src='" + datosRecetas[i].imgReceta + "'><div class='card__content'>" + datosRecetas[i].nombre + "</div></div></div>")

            } else {
                //alert("bien");
            }
        }
    }

    $(".card").click(ampliacionReceta);

}

function ampliacionReceta() {
    id = $(this).attr("data-id");

    $.ajax({
        url: "http://localhost/eBalanceBack/api/index.php",
        type: "GET",
        dataType: "JSON",
        data: {
            accion: "ampliacionReceta",
            datoElegido: id
        },
        success: mostrarAmpliacion,
    });

    document.getElementById('content').load("ampliacion.html");
}

function mostrarAmpliacion(datosAmpliacion) {
    $(".ingredientes").append("<p>" + datosAmpliacion[0].nombre + "</p>");
}

function agregarReceta() {
    document.getElementById('content').load("agregar.html");
}