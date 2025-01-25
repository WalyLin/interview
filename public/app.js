
var requestApi = {
    customer:{
        list : () => {
            return axios.get('/api/customers')
        },
        create:(data)=>{
            return axios.post('/api/customer/create',data)
        },
        update:(data)=>{
            return axios.put('/api/customer/update',data)
        },
        read:(data)=>{
            return axios.get('/api/customer/read/'+data.id)
        },
        delete:(data)=>{
            return axios.delete('/api/customer/delete/'+data.id)
        }
        

    },
    
}
