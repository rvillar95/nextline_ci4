<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>SignIn Boxed | CORK - Multipurpose Bootstrap Dashboard Template </title>
    <link rel="icon" type="image/x-icon" href="../src/assets/img/favicon.ico" />
    <link href="<?= base_url("lib/") ?>/layouts/vertical-light-menu/css/light/loader.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/") ?>layouts/vertical-light-menu/css/dark/loader.css" rel="stylesheet" type="text/css" />
    <script src="<?= base_url("lib/") ?>layouts/vertical-light-menu/loader.js"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="<?= base_url("lib/src/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet" type="text/css" />

    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/light/plugins.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/src/assets/css/light/authentication/auth-boxed.css") ?>" rel="stylesheet" type="text/css" />

    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/dark/plugins.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/src/assets/css/dark/authentication/auth-boxed.css") ?>" rel="stylesheet" type="text/css" />

    <!-- END GLOBAL MANDATORY STYLES -->

</head>

<body class="form">

    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <div class="auth-container d-flex">

        <div class="container mx-auto align-self-center">

            <div class="row">

                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center mx-auto">
                    <div class="card mt-3 mb-3">
                        <div class="card-body">
<?php

echo "<pre>";
//print_r($this->session);
echo "</pre>";

?>
                            <div class="row">
                                <div class="col-md-12 mb-3">

                                    <h2>Inicio de sesión</h2>
                                    <p>Ingresa tu correo y la clave</p>

                                </div>
                                <form method="POST" action="<?= base_url('inicio-sesion'); ?>" >

                                    <?= csrf_field(); ?>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Correo</label>
                                            <input type="email" id="correo" name="correo" value="<?php set_value('correo'); ?>" class="form-control" required autofocus >
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label">Clave</label>
                                            <input type="password" id="clave" name="clave" class="form-control" >
                                        </div>
                                    </div>
                                    <?php if (session()->getFlashdata('errors') !== null) : ?>

                                        <div class="alert alert-danger my-3" role="alert">
                                            <?= session()->getFlashdata('errors'); ?>
                                        </div>

                                    <?php endif; ?>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-secondary w-100">Entrar</button>
                                        </div>
                                    </div>
                                </form>



                                <!--               <div class="col-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input me-3" type="checkbox" id="form-check-default">
                                            <label class="form-check-label" for="form-check-default">
                                                Remember me
                                            </label>
                                        </div>
                                    </div>
                                </div> -->



                                <!--                            <div class="col-12 mb-4">
                                    <div class="">
                                        <div class="seperator">
                                            <hr>
                                            <div class="seperator-text"> <span>Or continue with</span></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4 col-12">
                                    <div class="mb-4">
                                        <button class="btn  btn-social-login w-100 ">
                                            <img src="../src/assets/img/google-gmail.svg" alt="" class="img-fluid">
                                            <span class="btn-text-inner">Google</span>
                                        </button>
                                    </div>
                                </div>
    
                                <div class="col-sm-4 col-12">
                                    <div class="mb-4">
                                        <button class="btn  btn-social-login w-100">
                                            <img src="../src/assets/img/github-icon.svg" alt="" class="img-fluid">
                                            <span class="btn-text-inner">Github</span>
                                        </button>
                                    </div>
                                </div>
    
                                <div class="col-sm-4 col-12">
                                    <div class="mb-4">
                                        <button class="btn  btn-social-login w-100">
                                            <img src="../src/assets/img/twitter.svg" alt="" class="img-fluid">
                                            <span class="btn-text-inner">Twitter</span>
                                        </button>
                                    </div>
                                </div> 

                                <div class="col-12">
                                    <div class="text-center">
                                        <p class="mb-0">Dont't have an account ? <a href="javascript:void(0);" class="text-warning">Sign Up</a></p>
                                    </div>
                                </div>-->

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="<?= base_url("lib/src/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <script src="<?= base_url("lib/js/jquery.js") ?>"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script>
        /*        $("#iniciarSesion").click(function(e) {
            e.preventDefault();
            let correo = $("#correo").val();
            let clave = $("#clave").val();
            if (correo != "") {
                if (clave != "") {
                    $.ajax({
                        url: 'inicio-sesion',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            correo,
                            clave
                        }
                    }).then(function(msg) {
                        if (msg == "1") {
                            $("#correo").val("");
                            $("#clave").val("");
                            toastr.success("Inicio de sesion correcto, redireccionando.");
                        } else {
                            toastr.error("Error al iniciar sesion.");
                        }
                    });
                } else {
                    toastr.error("Ingrese su clave.");
                    $("#clave").focus();
                }
            } else {
                toastr.error("Ingrese su correo.");
                $("#correo").focus();
            }
        }); */
    </script>

</body>

</html>