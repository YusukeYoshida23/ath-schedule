import { createI18n } from 'vue-i18n'
import ja from './locales/ja.js'

const i18n = createI18n({
  legacy: false,
  locale: 'ja',
  messages: {
    ja
  }
})

export default i18n
