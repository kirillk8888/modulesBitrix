var task = new Vue({
    el: '#task',
    data() {
        return {
            ip: localStorage.ip
        }
    },
    created: function () {
        var self = this;
        $.get("https://ipinfo.io", function(response) {
            self.ip = response.ip;
        }, "jsonp");
    },
    watch: {
        ip: function  () {
            if (localStorage.ip == this.ip) {
                return false;
            }
            localStorage.ip = this.ip;
            let requestArray = {
                "ip" : this.ip
            };
            let queryString = JSON.stringify(requestArray);
            axios.post('/', queryString)
                .then((response) => {
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    }
});