<html>

<head>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script src="https://unpkg.com/dayjs/dayjs.min.js"></script>
    <script src="https://unpkg.com/dayjs/plugin/customParseFormat.js"></script>
    <script src="https://unpkg.com/dayjs/plugin/weekday.js"></script>
    <script src="https://unpkg.com/dayjs/plugin/localeData.js"></script>
    <script src="https://unpkg.com/dayjs/plugin/weekOfYear.js"></script>
    <script src="https://unpkg.com/dayjs/plugin/weekYear.js"></script>
    <script src="https://unpkg.com/dayjs/plugin/advancedFormat.js"></script>
    <script src="https://unpkg.com/dayjs/plugin/quarterOfYear.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ant-design-vue@4.2.6/dist/antd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/ant-design-vue@4.2.6/dist/reset.min.css" rel="stylesheet">
</head>

<body>
    <div id="app">
        <a-flex style="height:100%;width:100%" justify="center" align="center" vertical>
            <a-flex style="width:100%" justify="center" align="center">

                <a-form style="width:400px" :label-col="{ span: 5 }">
                    <a-form-item label="邮箱">
                        <a-input v-model:value="form.email"></a-input>
                    </a-form-item>

                    <a-form-item label="密码">
                        <a-input-password v-model:value="form.password"></a-input-password>
                    </a-form-item>


                    <a-form-item label="邮箱验证码">
                        <a-input v-model:value="form.verify_code" />
                    </a-form-item>

                    <a-form-item label="" style="float:right;position:relative;top:-55;right:5">
                        <a-button @click="sendcode" type="link">获取验证码</a-button>
                    </a-form-item>

                    <a-form-item style="clear:both;" :wrapper-col="{ offset: 5, span: 16 }">
                        <a-button size="large" @click="handleSubmit" type="primary" html-type="submit">登陆</a-button>
                    </a-form-item>

                </a-form>
            </a-flex>
        </a-flex>
    </div>
    <script>
        const { createApp, ref } = Vue

        const app = createApp({
            setup() {

                const token = ref(localStorage.getItem('user-token') || '');

                if(token.length > 0){
                    window.location.href = '/customer';
                }
                
                const form = ref({

                    password: '12345678',
                    email: 'waly@gmail.com',
                    name: 'waly',
                    verify_code: '',
                    // _token: '{{csrf_token()}}',
                });

                const handleSubmit = async () => {
                    axios.post('{{route('login')}}', form.value)
                        .then(function (response) {
                            if (response.data.code == 200) {
                                antd.message.success(response.data.message);
                                token.value = response.data.data.access_token;                                
                                localStorage.setItem('user-token', token.value);
                                location.href = '/api/customer';
                            } else {
                                antd.message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            error.response?.data?.message ?
                                antd.message.error(error.response.data.message) :
                                antd.message.error(error.message);
                        });
                };

                const sendcode = async () => {
                    axios.post('{{route('sendcode')}}', form.value)
                        .then(function (response) {
                            if (response.data.code == 200) {
                                antd.message.success(response.data.message);
                                antd.message.success(response.data.data);
                            } else {
                                antd.message.error(response.data.message);
                            }

                        })
                        .catch(function (error) {
                            error.response?.data?.message ?
                                antd.message.error(error.response.data.message) :
                                antd.message.error(error.message);

                        });
                    return false;
                };

                

                // 请求拦截器，用于在每个请求中添加token
                axios.interceptors.request.use(config => {
                    
                    if (token.value) {
                        config.headers.Authorization = `Bearer ${token.value}`;
                    }
                    return config;
                }, error => {
                    return Promise.reject(error);
                });

                return {
                    form,
                    sendcode,
                    
                    handleSubmit
                }
            }
        })

        app.use(antd).mount('#app')


    </script>
</body>

</html>