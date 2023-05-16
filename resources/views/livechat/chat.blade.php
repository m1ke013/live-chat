<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>
    <script
    src="https://code.jquery.com/jquery-3.7.0.min.js"
    integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
    crossorigin="anonymous"></script>
    <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js" integrity="sha512-XdUZ5nrNkVySQBnnM5vzDqHai823Spoq1W3pJoQwomQja+o4Nw0Ew1ppxo5bhF2vMug6sfibhKWcNJsG8Vj9tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.2/vue.cjs.min.js" integrity="sha512-Nk4aRxW2zg0jN2rFYzF1Hc/xiTNTkk1LwMytaVrL7hK4Qij51+H7oNmKzDDYdUQzEEWAgBhzQ7F6bGYesDi1Bw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.0/vue.global.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.14/vue.min.js" integrity="sha512-BAMfk70VjqBkBIyo9UTRLl3TBJ3M0c6uyy2VMUrq370bWs7kchLNN9j1WiJQus9JAJVqcriIUX859JOm12LWtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container" id="app">
        <br>
        <br>
        <h1 class="text-center p-0 m-0">Live Chat</h1>
        <div class="card mt-4">
          <div class="card-header p-2">
            <form>
              <div class="col-lg-2 col-md-3 col-sm-12 mt-2 p-0">
                <label>Name</label>
                <input class="form-control form-control-sm" placeholder="Name" v-model="name">
              </div>
              <div class="col-lg-1 col-md-2 col-sm-12 mt-2 p-0">
                <button v-if="connected === false" v-on:click="connect()" type="button"
                        class="mr-2 btn btn-sm btn-primary w-100">
                  Connect
                </button>
                <button v-if="connected === true" v-on:click="disconnect()" type="button"
                        class="mr-2 btn btn-sm btn-danger w-100">
                  Disconnect
                </button>
              </div>
            </form>
            <div>
              <p>Channels current state is @{{ state }}</p>
            </div>
          </div>
          <div v-if="connected === true" class="card-body">
    
            <div class="col-12 bg-light pt-2 pb-2 mt-3">
              <p class="p-0 m-0 ps-2 pe-2" v-for="(message, index) in incomingMessages">
                (@{{ message.time }}) <b>@{{ message.name }}</b>
                @{{ message.message }}
              </p>
            </div>
    
            <h4 class="mt-4">Message</h4>
            <form>
              <div class="row mt-2">
                <div class="col-12 text-white" v-show="formError === true">
                  <div class="bg-danger p-2 mb-2">
                    <p class="p-0 m-0"><b>Error:</b> Invalid message.</p>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <textarea v-model="message" placeholder="Your message ..." class="form-control" rows="3"></textarea>
                  </div>
                </div>
              </div>
              <div class="row text-right mt-2">
                <div class="col-lg-10">
    
                </div>
                <div class="col-lg-2">
                  <button type="button" v-on:click="sendMessage()" class="btn btn-small btn-primary w-100">Send
                    Message</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    <script>
          new Vue({
            "el": "#app",
            "data": {
                connected: false,
                pusher: null, // something here
                app: null,
                apps: {!! json_encode($apps) !!},
                logChannel: "{{ $logChannel }}",
                authEndpoint: "{{ $authEndpoint }}",
                host: "{{ $host }}",
                port: "{{ $port }}",
                state: null,
                message: null,
                name: null,
                formError: false,
                incomingMessages: [
                ],
            },     
            mounted() {
                this.app = this.apps[0] || null;
            },
            methods: {
                connect() {
                    
                    this.pusher = new Pusher("staging", {
                        cluster: "mt1",
                        wsHost: "127.0.0.1",
                        wsPort: "6001",
                        wssPort: this.port,
                        wsPath: this.app.path,
                        disableStats: true,
                        authEndpoint: this.authEndpoint,
                        forceTLS: false,
                        auth: {
                        headers: {
                            "X-CSRF-Token": "{{ csrf_token() }}",
                            "X-App-ID": this.app.id
                        }
                        },
                        enabledTransports: ["ws", "flash"]
                    });

                    this.pusher.connection.bind('state_change', states => {
                        this.state = states.current
                    });

                    this.pusher.connection.bind('connected', () => {
                        this.connected = true;
                    });

                    this.pusher.connection.bind('disconnected', () => {
                        this.connected = false;
                    });

                    this.pusher.connection.bind('error', event => {
                        this.formError = true;
                    });

                    this.subscribeToAllChannels();
                },
                subscribeToAllChannels() {
                    [
                        "api-message",
                    ].forEach(channelName => this.subscribeToChannel(channelName));
                },
                subscribeToChannel(channelName) {
                    // var pusher = new Pusher("APP_KEY");
                    // private-websockets-dashboard-api-message
                    var channel = this.pusher.subscribe(this.logChannel+channelName);
                    let inst = this;
                    var callback = (data) => {
                        if (data.type === "api-message") {
                            if (data.details.includes("SendMessageEvent")) {
                                // still having issue 
                                let messageData = JSON.parse(data.data);
                                let utcDate = new Date(messageData.time);
                                messageData.time = utcDate.toLocaleString();
                                inst.incomingMessages.push(messageData);
                            }
                        }

                    };

                    channel.bind("log-message", callback);  
                },
                disconnect() {
                this.connected = false;
                },
                sendMessage() {
                this.formError = false;
                if (this.message === "" || this.message === null) {
                    this.formError = true;
                } else {
                    $.post("/chat/send", {
                        _token: '{{ csrf_token() }}',
                        message: this.message,
                        name: this.name,
                        time: this.time
                    }).fail(() => {
                        alert("Error sending event.")
                    });
                }
                }
            }
            });
    </script>
</body>
</html>