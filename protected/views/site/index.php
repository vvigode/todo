<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div id="app" class="container py-5">
    <h1 class="mb-4 text-center">Список задач</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form class="input-group mb-3" @submit.prevent="addTask">
                <input type="text" class="form-control" v-model="newTitle" placeholder="Новая задача" />
                <button class="btn btn-primary" type="submit">Добавить</button>
            </form>

            <ul class="list-group" v-if="tasks.length">
                <li v-for="task in tasks"
                    :key="task.id"
                    class="list-group-item d-flex justify-content-between align-items-center"
                    :class="{'list-group-item-success': task.is_done}">
                    <span :class="{'text-decoration-line-through text-muted': task.is_done}">{{ task.title }}</span>
                    <span v-if="task.is_done" class="badge bg-success">✓</span>
                </li>
            </ul>
            <p v-else class="text-muted mb-0">Задач пока нет. Добавьте первую!</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script>
    new Vue({
        el: '#app',
        data: {
            tasks: [],
            newTitle: ''
        },
        created() {
            this.fetchTasks();
        },
        methods: {
            fetchTasks() {
                fetch('/api/list')
                    .then(r => r.json())
                    .then(data => this.tasks = data);
            },
            addTask() {
                const title = this.newTitle.trim();
                if (!title) return;

                fetch('/api/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: 'title=' + encodeURIComponent(title)
                })
                    .then(r => r.json())
                    .then(task => {
                        this.tasks.unshift(task);
                        this.newTitle = '';
                    });
            }
        }
    });
</script> 