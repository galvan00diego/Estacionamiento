
$(document).ready(function () {
    $("#FormLogin").bootstrapValidator(

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
                loginemail:
                {
                    validators:
                    {
                        notEmpty:
                        {
                            message: 'Campo vacio'
                        },
                        emailAddress:
                        {
                            message: 'No es un email válido'
                        }
                    }
                },
                loginpass:
                {
                    validators:
                    {
                        notEmpty:
                        {
                            message: 'Campo vacio'
                        },
                        stringLength:
                        {
                            min: 4,
                            max: 8,
                            message: "La contraseña debe contener 4 a 8 caracteres"
                        }
                    }
                }
            }
        }

    ).on("success.form.bv", function (form) {

        form.preventDefault();
        var email=$("#loginemail").val();
        var pass=$("#loginpass").val();

        $.ajax({
            url: "./api.php/login",
            type: "POST",
            data: {"loginemail":email,
                   "loginpass":pass
            },
            dataType: 'text'
           
        }).done(function (res) {

                if(res)
                {
                localStorage.setItem("token",res);
                location.href = "principal.html";
                }
                else
                {
                    swal({
                        text:"Error, usuario no encontrado",
                        type:"error"
                    }).then((result) => {
                    location.href="index.html";
                    });
                }
           
        }).fail(function (res) {

            alert("error " + res);
        });


    });

    $("#FormRegistro").bootstrapValidator(
        
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
                        foto:
                        {
                            validators:
                            {
                                notEmpty:
                                {
                                    message: 'Por favor seleccione una imagen'
                                },
                                file:
                                {
                                    extension: 'jpg,png',
                                    type: 'image/jpeg,image/png',
                                    message: 'El archivo seleccionado no es valido'
                                },
                                file:
                                {
                                    maxSize:850000,
                                    message:'El archivo es demasiado grande. (Maximo 850kb)'
                                }
                            }
                        },
                        nombre:
                        {
                            validators:
                            {
                                notEmpty:
                                {
                                    message: 'Campo vacio'
                                },
                                stringLength:
                                {
                                    max: 30,
                                    message: "El nombre no debe contener mas de 30 caracteres"
                                }
                            }
                        },
                        perfil:
                        {
                            validators:
                            {
                                notEmpty:
                                {
                                    message: 'Campo vacio'
                                }
                            }
                        },
                        email:
                        {
                            validators:
                            {
                                notEmpty:
                                {
                                    message: 'Campo vacio'
                                },
                                emailAddress:
                                {
                                    message: 'No es un email válido'
                                },
                                stringLength:
                                {
                                    max: 30,
                                    message: "El email no debe contener mas de 30 caracteres"
                                }
                            }
                        },
                        clave:
                        {
                            validators:
                            {
                                notEmpty:
                                {
                                    message: 'Campo vacio'
                                },
                                stringLength:
                                {
                                    min: 4,
                                    max: 8,
                                    message: "La contraseña debe contener 4 a 8 caracteres"
                                },
                                identical: 
                                {
                                    field: 'Rclave',
                                    message: 'Las contraseñas no coinciden'
                                }
                            }
                        },
                        Rclave:
                        {
                            validators:
                            {
                                notEmpty:
                                {
                                    message: 'Campo vacio'
                                },
                                identical: 
                                {
                                    field: 'clave',
                                    message: 'Las contraseñas no coinciden'
                                }
                            }
                        }
                    }
                }
        
            ).on("success.form.bv", function (form) {
        
                form.preventDefault();
        
                var formData = new FormData($(this)[0]);
                var archivo = document.getElementById("foto");
                formData.append("foto", archivo.files[0]);
                
        
                $.ajax({
                    url: "./api.php/registro",
                    type: "POST",
                    data: formData,
                    dataType: 'text',
                    contentType: false,
                    cache: false,
                    processData: false
                }).done(function (res) {
                    swal({
                        title: 'Registro Exitoso!',
                        text: 'Do you want to continue',
                        type: 'error',
                        confirmButtonText: 'Cool'
                      });
                    // swal(
                    //     'Registro Exitoso!!',
                    //     'Ya puede iniciar sesión.',
                    //     'success'
                    //   );
                    location.href = "index.html";
        
                }).fail(function (res) {
        
                    swal(
                        'Oops...',
                        'Something went wrong!',
                        'error'
                      )
                });
        
        
            });
});

