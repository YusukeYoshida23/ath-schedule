<template>
  <div class="container mt-4">
    <h2>{{ $t('dashboard.title') }}</h2>

    <!-- スケジュール作成フォーム -->
    <div class="card mb-4">
      <div class="card-body">
        <h3 class="card-title">{{ $t('dashboard.newSchedule') }}</h3>
        <form @submit.prevent="createSchedule">
          <div class="mb-3">
            <label for="title" class="form-label">{{ $t('dashboard.form.title') }}</label>
            <input type="text" class="form-control" id="title" v-model="newSchedule.title" required>
          </div>
          <div class="mb-3">
            <label for="start_time" class="form-label">{{ $t('dashboard.form.startTime') }}</label>
            <input type="datetime-local" class="form-control" id="start_time" v-model="newSchedule.start_time" required>
          </div>
          <div class="mb-3">
            <label for="end_time" class="form-label">{{ $t('dashboard.form.endTime') }}</label>
            <input type="datetime-local" class="form-control" id="end_time" v-model="newSchedule.end_time" required>
          </div>
          <button type="submit" class="btn btn-primary">{{ $t('dashboard.form.submit') }}</button>
        </form>
      </div>
    </div>

    <!-- スケジュール一覧 -->
    <div class="schedule-list">
      <div v-if="loading" class="text-center">
        <p>{{ $t('dashboard.loading') }}</p>
      </div>
      <div v-else-if="schedules.length === 0" class="alert alert-info">
        {{ $t('dashboard.noSchedules') }}
      </div>
      <div v-else class="list-group">
        <div v-for="schedule in schedules" :key="schedule.id" class="list-group-item">
          <!-- 表示モード -->
          <div v-if="!schedule.editing">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-1">{{ schedule.title }}</h5>
              <div class="btn-group">
                <button @click="startEdit(schedule)" class="btn btn-outline-primary btn-sm">
                  {{ $t('dashboard.actions.edit') }}
                </button>
                <button @click="deleteSchedule(schedule.id)" class="btn btn-outline-danger btn-sm">
                  {{ $t('dashboard.actions.delete') }}
                </button>
              </div>
            </div>
            <p class="mb-1">
              {{ formatDateTime(schedule.start_time) }} 〜 {{ formatDateTime(schedule.end_time) }}
            </p>
          </div>
          <!-- 編集モード -->
          <div v-else>
            <form @submit.prevent="updateSchedule(schedule)">
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.form.title') }}</label>
                <input type="text" class="form-control" v-model="schedule.title" required>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.form.startTime') }}</label>
                <input type="datetime-local" class="form-control" v-model="schedule.start_time" required>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('dashboard.form.endTime') }}</label>
                <input type="datetime-local" class="form-control" v-model="schedule.end_time" required>
              </div>
              <div class="btn-group">
                <button type="submit" class="btn btn-primary btn-sm">{{ $t('dashboard.actions.save') }}</button>
                <button type="button" @click="cancelEdit(schedule)" class="btn btn-secondary btn-sm">
                  {{ $t('dashboard.actions.cancel') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { format } from 'date-fns'
import { ja } from 'date-fns/locale'
import api from '@/services/api'

const schedules = ref([])
const loading = ref(true)
const newSchedule = ref({
  title: '',
  start_time: '',
  end_time: ''
})

// スケジュール一覧を取得
const fetchSchedules = async () => {
  try {
    loading.value = true
    const response = await api.get('/schedules')
    schedules.value = response.data
  } catch (error) {
    console.error('スケジュールの取得に失敗しました:', error)
  } finally {
    loading.value = false
  }
}

// 新規スケジュール作成
const createSchedule = async () => {
  try {
    await api.post('/schedules', newSchedule.value)
    newSchedule.value = { title: '', start_time: '', end_time: '' }
    await fetchSchedules()
  } catch (error) {
    console.error('スケジュールの作成に失敗しました:', error)
  }
}

// スケジュールの編集開始
const startEdit = (schedule) => {
  schedule.editing = true
  schedule.originalData = { ...schedule }
}

// 編集のキャンセル
const cancelEdit = (schedule) => {
  Object.assign(schedule, schedule.originalData)
  delete schedule.editing
  delete schedule.originalData
}

// スケジュールの更新
const updateSchedule = async (schedule) => {
  try {
    await api.put(`/schedules/${schedule.id}`, {
      title: schedule.title,
      start_time: schedule.start_time,
      end_time: schedule.end_time
    })
    delete schedule.editing
    delete schedule.originalData
    await fetchSchedules()
  } catch (error) {
    console.error('スケジュールの更新に失敗しました:', error)
  }
}

// スケジュールの削除
const deleteSchedule = async (id) => {
  if (!confirm(t('dashboard.confirmDelete'))) return

  try {
    await api.delete(`/schedules/${id}`)
    await fetchSchedules()
  } catch (error) {
    console.error('スケジュールの削除に失敗しました:', error)
  }
}

// 日時のフォーマット
const formatDateTime = (datetime) => {
  return format(new Date(datetime), 'M月d日(E) HH:mm', { locale: ja })
}

// 初期読み込み
onMounted(() => {
  fetchSchedules()
})
</script>
