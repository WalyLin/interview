<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo</title>
    <!-- 请勿在项目正式环境中引用该 layui.css 地址 -->
    <link href="//unpkg.com/layui@2.9.21/dist/css/layui.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/ant-design-vue@4.2.6/dist/reset.min.css" rel="stylesheet">
    <!-- 请勿在项目正式环境中引用该 layui.js 地址 -->
    <script src="//unpkg.com/layui@2.9.21/dist/layui.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="/app.js"></script>

</head>

<body>
    <div id="app" style="padding:100px">
        <a-button type="primary" @click="openModal('新增','')">新增</a-button>
        <table class="layui-table" lay-even>
            <colgroup>
                <col width="150">
                <col width="150">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>id</th>
                    <th>first_name</th>
                    <th>last_name</th>
                    <th>邮箱</th>
                    <th>年龄</th>
                    <th>出生年月</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @verbatim
                    <tr v-for="item in list">
                        <td>{{item . id}}</td>
                        <td>{{item . first_name}}</td>
                        <td>{{item . last_name}}</td>
                        <td>{{item . email}}</td>
                        <td>{{item . age}}</td>
                        <td>{{item . dob}}</td>
                        <td>{{item . creation_date}}</td>
                        <td>
                            <a class="layui-btn layui-btn-primary layui-btn-sm" @click="detail(item)">详情</a>
                            <a class="layui-btn layui-btn-primary layui-btn-sm" @click="openModal('编辑',item)">编辑</a>
                            <a-popconfirm title="Are you sure?" ok-text="Yes" cancel-text="No" @confirm="del(item)">
                                <a class="layui-btn layui-btn-danger layui-btn-sm">删除</a>
                            </a-popconfirm>

                        </td>
                    </tr>
                @endverbatim
            </tbody>
        </table>

        @verbatim
            <a-modal v-model:open="isVisible" :title="modalTitle" @ok="handleOk">
                <a-form :model="formState" :label-col="{ span: 5 }">
                    <a-form-item label="first name">
                        <a-input v-model:value="formState.first_name" placeholder="" />
                    </a-form-item>

                    <a-form-item label="last name">
                        <a-input v-model:value="formState.last_name" placeholder="" />
                    </a-form-item>

                    <a-form-item label="age">
                        <a-input v-model:value="formState.age" placeholder="" />
                    </a-form-item>

                    <a-form-item label="email">
                        <a-input v-model:value="formState.email" placeholder="" />
                    </a-form-item>

                    <a-form-item name="dob" label="dob">
                        <a-date-picker v-model:value="formState['dob']" value-format="YYYY-MM-DD" />
                    </a-form-item>


                </a-form>
            </a-modal>
        @endverbatim

    </div>


</body>
<script>
    const { createApp, ref, onMounted } = Vue


    if(!localStorage.getItem('user-token')){
        window.location.href = '/login';
    }

    const app = createApp({
        setup() {
            const show = ref(false);
            const isVisible = ref(false);
            const token = ref(localStorage.getItem('user-token') || '');
            const list = ref([]);
            const currentItem = ref({});
            const modalTitle = ref('');
            const formState = ref({
                id: '',
                first_name: '',
                last_name: '',
                age: '',
                email: '',
                dob: '',
                creation_date: '',
            });

            // 请求拦截器，用于在每个请求中添加token
            axios.interceptors.request.use(config => {

                if (token.value) {
                    config.headers.Authorization = `Bearer ${token.value}`;
                }
                return config;
            }, error => {
                return Promise.reject(error);
            });


            const reload = () => {
                if(!token.value){
                    window.location.href = '/login';
                }

                requestApi.customer.list().then(function (response) {
                    if (response.data.code == 200) {
                        list.value = response.data.data
                        show.value = true
                    }
                }).catch(function (error) {
                    error.response?.data?.message ?
                        antd.message.error(error.response.data.message) :
                        antd.message.error(error.message);

                });
            }

            // 更新
            const update = (item) => {
                item.dob = dayjs(item.dob).format('YYYY-MM-DD')
                requestApi.customer.update(item).then(function (response) {
                    if (response.data.code == 200) {
                        antd.message.success('操作成功')
                        closeModal()
                        reload()
                    }
                }).catch(function (error) {
                    error.response?.data?.message ?
                        antd.message.error(error.response.data.message) :
                        antd.message.error(error.message);
                });
            }
            // 详情
            const detail = (item) => {
                requestApi.customer.read(item).then(function (response) {
                    if (response.data.code == 200) {
                        openModal('详情', response.data.data)
                    }
                }).catch(function (error) {
                    error.response?.data?.message ?
                        antd.message.error(error.response.data.message) :
                        antd.message.error(error.message);
                });

            }
            // 删除
            const del = (item) => {
                requestApi.customer.delete(item).then(function (response) {
                    if (response.data.code == 200) {
                        antd.message.success('删除成功')
                        reload()
                    }
                }).catch(function (error) {
                    error.response?.data?.message ?
                        antd.message.error(error.response.data.message) :
                        antd.message.error(error.message);
                });
            }

            // 新增
            const create = (item) => {
                
                let dob = dayjs(item.dob).format('YYYY-MM-DD');
                if(dayjs(dob).isValid()){
                    item.dob = dob;
                }
                
                
                requestApi.customer.create(item).then(function (response) {
                    if (response.data.code == 200) {
                        antd.message.success('操作成功')
                        closeModal()
                        reload()
                    }
                }).catch(function (error) {
                    error.response?.data?.message ?
                        antd.message.error(error.response.data.message) :
                        antd.message.error(error.message);
                });
            }

            const openModal = (title, item) => {
                modalTitle.value = title
                isVisible.value = true
                if (item) {
                    formState.value = _.cloneDeep(item)
                } else {
                    formState.value = {
                        id: '',
                        first_name: '',
                        last_name: '',
                        age: '',
                        email: '',
                        dob: '',
                        creation_date: '',
                    }
                }
            }

            const handleOk = () => {
                if (modalTitle.value == '新增') {
                    create(formState.value)
                } else if (modalTitle.value == '编辑') {
                    update(formState.value)
                } else {
                    closeModal()
                }

            }

            const closeModal = () => {
                isVisible.value = false
            }

            // 使用 onMounted 钩子
            onMounted(() => {
                reload()
            });

            return {
                currentItem,
                modalTitle,
                formState,
                isVisible,
                handleOk,
                openModal,
                list,
                reload,
                update,
                detail,
                del,
                create
            }
        }
    })
    app.use(antd)
    app.mount('#app')
</script>

</html>