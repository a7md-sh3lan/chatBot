<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Chat Bot</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .bot-response {
                max-width: 400px;
                word-break: break-word;
            }

            .my-response {
                max-width: 400px;
                word-break: break-word;
            }

            .modal-body {
                height: 400px;
                overflow-y: auto;
            }

            .hidden {
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    Hello, How Are You?!
                </div>

                <p>Please, fill your data..</p>

                <div class="links">
                <form>
                    <div class="form-group">
                        <label for="phone_number">phone number</label>
                        <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="01xxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label for="name">your name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Tom Hanks">
                    </div>
                    <button id="first_form" type="submit" class="btn btn-primary" link="{{route('checkGuest')}}">Meet our bot</button>
                </form>
                </div>
            </div>
        </div>



        <!-- Modal -->
        <div class="modal fade" id="chatBotModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 700px;">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-heigh:400px; overflow-y:auto;">
                
            </div>
            <div class="modal-footer">
                <input type="text" class="form-control" name="message" id="message" placeholder="Your response">
                <button type="button" class="btn btn-primary submitBtn" phase="">Send Response</button>
            </div>
            <div class="alert alert-danger hidden error-msg">please Enter something..</div>
            </div>
        </div>
        </div>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <!-- My script -->
        <!-- Script -->
        <script>
            $("#first_form").on("click", function(e){
                e.stopPropagation();
                e.preventDefault();
                let phone = $("#phone_number").val();
                let name  = $("#name").val();
                let url   = $(this).attr("link");

                $.ajax({
                    type: "post",
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        phone_number : phone,
                        name : name,
                    },

                    success: function(response) {
                        console.log(response);
                        $(".modal-body").html('');
                        if(!response.status) {
                            $("#exampleModalLongTitle").text('Welcome in your first visit..');
                            $(".modal-body").append(`<div class="alert alert-primary bot-response">Hey, ${name}; This is your first visit.</div>`);
                            $(".modal-body").append(`<div class="alert alert-primary bot-response">Please Enter the id or the name of one of these Categories: </div>`);
                            response.categories.forEach(element => $(".modal-body").append(`<div class="alert alert-primary bot-response"> ${element.id} - ${element.name} </div>`));
                            $(".submitBtn").attr("phase", "category");
                            $("#chatBotModal").modal();
                        } else if(response.status && !response.session) {
                            $("#exampleModalLongTitle").text('Welcome again..');
                            $(".modal-body").append(`<div class="alert alert-primary bot-response">Hey, ${name}; Welcome Again.</div>`);
                            $(".modal-body").append(`<div class="alert alert-primary bot-response">Please Enter the id or the name of one of these Categories: </div>`);
                            response.categories.forEach(element => $(".modal-body").append(`<div class="alert alert-primary bot-response"> ${element.id} - ${element.name} </div>`));
                            $(".submitBtn").attr("phase", response.phase);
                            $("#chatBotModal").modal();
                        } else {
                            $("#exampleModalLongTitle").text('Welcome again..');
                            $(".modal-body").append(`<div class="alert alert-primary bot-response">Hey, ${name}; Welcome Again.</div>`);
                            if(response.phase != 'confirm') {
                                $(".modal-body").append(`<div class="alert alert-primary bot-response">You stopped on ${response.phase} select: </div>`);
                                response.target.forEach(element => $(".modal-body").append(`<div class="alert alert-primary bot-response"> ${element.id} - ${element.name} </div>`));
                                if(response.phase != 'category') {
                                    $(".modal-body").append(`<div class="alert alert-primary bot-response">If you want to back to the main menu send 0 or back. </div>`);
                                }
                            } else {
                                $(".modal-body").append(`<div class="alert alert-primary bot-response">You stopped on ${response.phase} confirm: </div>`);
                                $(".modal-body").append(`<div class="alert alert-primary bot-response">We have ${response.target.quantity} pieces from ${response.target.name}, and you can buy it for ${response.target.price} AED.</div>`);
                                $(".modal-body").append(`<div class="alert alert-primary bot-response">If you want to buy it send 1 or ok, else you can enter 0 or back to return to the main menu.</div>`);
                            }
                            $(".submitBtn").attr("phase", response.phase);
                            $("#chatBotModal").modal();
                        }
                    }
                });
            });

            $(".submitBtn").on("click", function() {
                let response = $("#message").val();
                let phase = $(this).attr("phase");
                let phone = $("#phone_number").val();
                $(".modal-body").append(`<div class="alert alert-dark my-response ml-auto">${response}</div>`);
                $("#message").val('');
                if(response.trimLeft().length < 1) {
                    $(".error-msg").show();
                } else {
                    $(".error-msg").hide();
                    $.ajax({
                        type: "post",
                        url: '/sendToBot',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            order : response,
                            phase : phase,
                            phone: phone,
                        },

                        success: function(response) {
                            if(response.status && response.phase == 'payement' && response.target) {
                                $("#chatBotModal").modal('hide');
                                alert('Calling Payement Gateway..');
                            }
                            if(response.status && response.phase != 'confirm') {
                                if(response.target.length) {
                                    $(".modal-body").append(`<div class="alert alert-primary bot-response">Please Enter the id or the name of one of these options: </div>`);
                                    response.target.forEach(element => $(".modal-body").append(`<div class="alert alert-primary bot-response"> ${element.id} - ${element.name} </div>`));
                                    $(".submitBtn").attr("phase", response.phase);
                                } else {
                                    $(".modal-body").append(`<div class="alert alert-primary bot-response">It seems this option is empty for now please choose another one.. </div>`);
                                }
                                if(response.phase != 'category') {
                                    $(".modal-body").append(`<div class="alert alert-primary bot-response">If you want to back to the main menu send 0 or back. </div>`);
                                }
                            } else if(response.status && response.phase == 'confirm' && response.target) {
                                $(".modal-body").append(`<div class="alert alert-primary bot-response">We have ${response.target.quantity} pieces from ${response.target.name}, and you can buy it for ${response.target.price} AED.</div>`);
                                $(".modal-body").append(`<div class="alert alert-primary bot-response">If you want to buy it send 1 or ok, else you can enter 0 or back to return to the main menu.</div>`);
                                $(".submitBtn").attr("phase", response.phase);
                            } else {
                                $(".modal-body").append(`<div class="alert alert-primary bot-response">Sorry! This is wrong choice. Please enter valid ${response.phase}.</div>`);
                            }
                            var scr = $('.modal-body')[0].scrollHeight;
                            $('.modal-body').animate({scrollTop: scr},2000);
                        },
                    });
                }
            });
        </script>
    </body>
</html>
