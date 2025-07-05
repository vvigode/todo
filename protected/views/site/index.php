<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body { background: #f4f6fa; }
    h1 { font-weight: 600; }
    .board-col { height: 70vh; overflow-y: auto; padding: 0 12px 24px; border-radius: 12px; display:flex; flex-direction:column; }
    .board-col.new   { background:#f0f4ff; }
    .board-col.progress { background:#fff8e6; }
    .board-col.done  { background:#e7ffef; }
    .board-header { position: sticky; top:0; z-index:10; background:rgba(255,255,255,.8); backdrop-filter:blur(4px); padding:12px 0; margin:0 -12px 12px; text-align:center; font-weight:600; border-bottom:1px solid #dee2e6; }
    .board-header span { font-size:22px; }
    .card.task-card { border: none; border-radius:12px; transition: transform .2s, box-shadow .2s; margin-bottom:16px; }
    .card.task-card:hover { transform: translateY(-4px); box-shadow:0 6px 16px rgba(0,0,0,.12); }
    .task-card img { border-top-left-radius:12px; border-top-right-radius:12px; max-height:160px; object-fit:cover; }
</style>

<div id="app" class="container py-5">
    <h1 class="mb-4 text-center">Список задач</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Форма добавления -->
            <form class="row g-2 mb-4" @submit.prevent="addTask">
                <div class="col-md-3">
                    <input type="text" class="form-control" v-model="newTitle" placeholder="Название *" required />
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" v-model="newImage" placeholder="URL картинки (опц.)" />
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" v-model="newDescription" placeholder="Описание (опц.)" />
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary" type="submit">+</button>
                </div>
            </form>

            <!-- Колонки категорий -->
            <div class="row">
                <div class="col-md-4 board-col" :class="cat.class" v-for="cat in categories" :key="cat.value">
                    <div class="board-header"><span>{{ cat.label }}</span></div>
                    <div v-for="task in filteredTasks(cat.value)" :key="task.id" class="card mb-3 task-card shadow-sm">
                        <img v-if="task.image" :src="task.image" class="card-img-top" style="object-fit:cover;height:140px;">
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1">{{ task.title }}</h6>
                            <p v-if="task.description" class="card-text small mb-0">{{ task.description }}</p>
                        </div>
                        <div class="card-footer bg-white py-1 d-flex justify-content-between align-items-center">
                            <select class="form-select form-select-sm w-auto" v-model.number="task.status" @change="changeStatus(task)">
                                <option :value="0">Новая</option>
                                <option :value="1">В работе</option>
                                <option :value="2">Сделано</option>
                            </select>
                            <button class="btn btn-sm btn-outline-danger" @click="deleteTask(task)">&times;</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script>
    new Vue({
        el: '#app',
        data: {
            tasks: [],
            newTitle: '',
            newDescription: '',
            newImage: '',
            categories: [
                { value: 0, label: 'Новая', class: 'new' },
                { value: 1, label: 'В работе', class: 'progress' },
                { value: 2, label: 'Сделано', class: 'done' }
            ]
        },
        created() {
            this.fetchTasks();
        },
        methods: {
            fetchTasks() {
                fetch('/api/list')
                    .then(r => r.json())
                    .then(data => {
                        this.tasks = data.map(t => ({ ...t, status: Number(t.status) }));
                    });
            },
            filteredTasks(status) {
                return this.tasks.filter(t => Number(t.status) === status);
            },
            addTask() {
                const title = this.newTitle.trim();
                if (!title) return;

                const form = new URLSearchParams();
                form.append('title', title);
                if (this.newDescription) form.append('description', this.newDescription);
                if (this.newImage) form.append('image', this.newImage);

                fetch('/api/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: form.toString()
                })
                    .then(r => r.json())
                    .then(task => {
                        this.tasks.unshift(task);
                        this.newTitle = '';
                        this.newDescription = '';
                        this.newImage = '';
                    });
            },
            changeStatus(task) {
                fetch('/api/status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                    body: `id=${task.id}&status=${task.status}`
                })
                    .then(r => r.json())
                    .then(updated => {
                        task.status = updated.status;
                        task.is_done = updated.is_done;
                    });
            },
            deleteTask(task) {
                if (!confirm('Удалить задачу?')) return;
                fetch('/api/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: `id=${task.id}`
                })
                    .then(r => r.json())
                    .then(resp => {
                        if (resp.success) {
                            this.tasks = this.tasks.filter(t => t.id !== task.id);
                        }
                    });
            }
        }
    });
</script> 