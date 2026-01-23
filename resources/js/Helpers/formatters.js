export function formatDate(date, options = {}) {
  if (!date) return '-'

  const defaultOptions = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }

  return new Date(date).toLocaleDateString('id-ID', { ...defaultOptions, ...options })
}

export function formatDateTime(datetime) {
  if (!datetime) return '-'

  return new Date(datetime).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

export function formatCurrency(amount, currency = 'IDR') {
  if (amount === null || amount === undefined) return '-'

  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount)
}

export function formatNumber(number) {
  if (number === null || number === undefined) return '-'

  return new Intl.NumberFormat('id-ID').format(number)
}

export function truncate(text, length = 50) {
  if (!text) return ''
  if (text.length <= length) return text

  return text.substring(0, length) + '...'
}
