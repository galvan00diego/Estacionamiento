function Salir() {
    swal({
        title: 'Quieres salir?',
        showCancelButton: true,
        background: '#fff url(./css/img/modal.jpg)',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            swal({
                title: 'Nos vemos!!',
                text: 'Vuelve pronto',
                background: '#fff url(./css/img/modal.jpg)',
                imageUrl: './css/img/saludar.gif'
            }).then(result => {
                location.href = "./index.html";
                localStorage.clear();
            })

        }
    })

}


/***********************MODALS ESPACIOS *************************************************/
    function Modal(modal)
    {
        $.ajax({
            url: "./api.php/cochera/autos",
            type: "GET",
            dataType: 'text',
            async:true
        }).done(function (res) {
            res=JSON.parse(res);
            console.log(res);
                for(let i=0;i<res.length;i++)
                {
                    document.getElementById("imgFoto").src = "./php/clases/img/libre.jpg";
                    $("#HEspacio").html("ESPACIO "+modal+" LIBRE");
                    $("#HPatente").html("Patente: LIBRE");
                    $("#HColor").html("Color: LIBRE");
                    $("#HMarca").html("Marca: LIBRE");
                    $("#HFechaIngreso").html("Fecha de Ingreso: LIBRE");
                    $("#btnEgresar").attr("disabled",true);
                    if(modal==res[i].id_cochera&&res[i].id_empleado_salida==0)
                    {
                        if(res[i].foto!="")
                        document.getElementById("imgFoto").src = "./php/clases/img/" + (res[i].foto);
                        $("#btnEgresar").attr("disabled",false);
                        $("#HPatente").html("Patente: "+res[i].patente);
                        $("#HEspacio").html("ESPACIO "+modal);
                        $("#HColor").html("Color: "+res[i].color);
                        $("#HMarca").html("Marca: "+res[i].marca);
                        $("#HFechaIngreso").html("Fecha de Ingreso: "+res[i].fecha_ingreso);
                        $("#btnEgresar").click(function(){
                            /***********SWAL ESTA SEGURO?? ****************/
                            swal({
                                title: 'Egresar Auto Patente: '+res[i].patente+'?',
                                showCancelButton: true,
                                background: '#fff url(./css/img/modal.jpg)',
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Si',
                                cancelButtonText: 'No'
                            }).then((result) => {
                                if (result.value) {
                                    $.ajax({
                                        url: "./api.php/administracion/autoout",
                                        type: "POST",
                                        data:{json:res[i],id_empleado_salida:localStorage.getItem("id_activo")},
                                        dataType: 'text',
                                        async:true
                                    }).done(function (res) {
                                        let data=JSON.parse(res);
                                        localStorage.setItem("tiempo",data.tiempo);
                                        $("#tiempo").html(res.tiempo);
                                        localStorage.setItem("importe",data.importe);
                                    }).fail(function (res){
                                        console.log("No se pudo egresar el auto.");
                                    });
                                    swal({
                                        title: 'Espacio '+modal+' Liberado!!',
                                        text:'Tiempo Transcurrido: '+$("#tiempo").val()+'Hs || IMPORTE: $'+localStorage.getItem("importe"),
                                        background: '#fff url(./css/img/modal.jpg)',
                                    }).then(result => {
                                        localStorage.removeItem("tiempo");
                                        localStorage.removeItem("importe");
                                        location.href = "./principal.html";
                                    })
                        
                                }
                            })
                            /******************************************/
                        })
                        break;
                    }
                }
            
            

        }).fail(function (res) {

            console.log("Error al listar");
        });
    }
/****************************************************************************************/

/*********************FECHA Y HORA ACTUAL ***************************/
    function startTime() 
    {
        var time = new Date();
        var mes = time.getMonth();

        var meses = new Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        var dia = new Array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

        today = new Date();
        h = today.getHours();
        m = today.getMinutes();
        s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        $("#fechaingreso").html(dia[time.getDay()] + " " + time.getDate() + " de " + meses[mes] + " del " + time.getFullYear());
        $('#reloj').html(h + ":" + m + ":" + s);
        t = setTimeout('startTime()', 500);
    }
    function checkTime(i) { if (i < 10) { i = "0" + i; } return i; }
    window.onload = function () { startTime(); }
/******************************************************************/

/********************DATOS DE USUARIO **********************************************************/
    $(document).ready(function () {
        
        if (localStorage.getItem("token")) {
            $.ajax({
                url: "./api.php/verificar",
                type: "POST",
                data: { "token": localStorage.getItem("token") },
                dataType: 'text'

            }).done(function (res) {

                if (res) {
                    var data = JSON.parse(res);
                    console.log("Usuario Activo: " + data["nombre"] + "\n Id User:" + data["id"]);
                    localStorage.setItem("id_activo",data["id"]);
                    $("#useremail").html(data["nombre"]);
                    $("#id").html(data["id"]);
                    document.getElementById("userimg").src = "./php/clases/img/" + (data["foto"]);
                }
                else {
                    swal({
                        text: "Error, usuario no encontrado",
                        type: "error"
                    }).then((result) => {
                        localStorage.clear();
                        location.href = "index.html";
                    });
                }

            }).fail(function (res) {

                alert("error " + res);
            });

/***********************************************************************************************/



/*************************TRAIGO ESPACIOS LIBRES ****************************************/
    $.ajax({
        url: "./api.php/cochera/listar",
        type: "GET",
        dataType: 'text',
        async:true
    }).done(function (res) {
        res=JSON.parse(res);
        
        for(var i=0;i<res.length;i++)
        {
            if(res[i].ocupada==0)
            {
                $("#espacio").html(res[i].numero);
                break;
            }
        }
        for(var i=0;i<res.length;i++)
        {
            if(res[i].ocupada==1)
            {
                let espacio=res[i].numero;
                $("#espacio"+espacio).css("background-color","red");
            }
        }

    }).fail(function (res) {

        console.log("Error al listar");
    });

    }
    else {
        location.href = "./index.html";
    }
/****************************************************************************************/
   


/*************************INGRESO DEL VEHICULO *****************************************/
    $("#FormIngreso").bootstrapValidator(
        {
            framework: 'bootstrap',
            icon:
                {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
            fields:
                {
                    patente:
                        {
                            validators:
                                {
                                    notEmpty:
                                        {
                                            message: 'Complete el campo'
                                        },
                                    stringLength:
                                        {
                                            min: 6,
                                            max: 7
                                        }
                                }
                        },
                    marca:
                        {
                            validators:
                                {
                                    notEmpty:
                                        {
                                            message: "Seleccione una marca"
                                        }
                                }
                        },
                    color:
                        {
                            validators:
                                {
                                    notEmpty:
                                        {
                                            message: "Seleccione una marca"
                                        }
                                }
                        },
                    foto:
                        {
                            validators:
                                {
                                    file:
                                        {
                                            extension: 'jpg,png',
                                            type: 'image/jpeg,image/png',
                                            message: 'El archivo seleccionado no es valido'
                                        },
                                    file:
                                        {
                                            maxSize: 850000,
                                            message: 'El archivo es demasiado grande. (Maximo 850kb)'
                                        }
                                }
                        }
                }
        }).on("success.form.bv", function (form) {
            form.preventDefault();
            var formData = new FormData($(this)[0]);
            var archivo = document.getElementById("foto");

            formData.append("foto", archivo.files[0]);

            formData.append("id_empleado_entrada", $("#id").html());


            $.ajax({
                url: "./api.php/administracion/autoin",
                type: "POST",
                data: formData,
                dataType: 'text',
                contentType: false,
                cache: false,
                processData: false
            }).done(function (res) {
                swal({
                    title: "Auto Ingresado!",
                    text: "Patente: " + formData.get("patente"),
                    imageUrl: './css/img/autoin.gif',
                    background: '#fff url(./css/img/modal.jpg)'
                })
                    .then(result => {
                        location.href = "./principal.html";
                    });
                console.log(res);

            }).fail(function (res) {

                swal(
                    'Oops...',
                    'Something went wrong!',
                    'error'
                )
            });
        });
/***************************************************************************************/
});