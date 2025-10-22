<template>
  <div class="min-h-screen bg-gray-100 py-8 print:bg-white print:py-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 print:px-0">
      
      <!-- Action Buttons -->
      <div class="mb-6 flex justify-end gap-3 print:hidden">
        <button
         @click="downloadPDF"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm font-medium transition-colors"
        >
          Print Report
        </button>
        <button
          @click="exportToExcel"
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded text-sm font-medium transition-colors"
        >
          Export to CSV
        </button>
      </div>

      <!-- Report Container -->
      <div class="bg-white shadow-sm print:shadow-none">
        <!-- Report Header -->
        <div class="border-b-2 border-gray-800 px-8 py-6 print:px-12 print:py-8">
          <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 uppercase tracking-wide">
              Parking System
            </h1>
            <h2 class="text-xl font-semibold text-gray-700 mt-2">
              Daily Parkout Report
            </h2>
            <p class="text-gray-600 mt-3 text-sm">
              Report Date: <span class="font-semibold">{{ reportDate }}</span>
            </p>
            <p class="text-gray-500 text-xs mt-1 hidden print:block">
              Generated: {{ new Date().toLocaleString('en-PH', { dateStyle: 'long', timeStyle: 'short' }) }}
            </p>
          </div>
        </div>

        <!-- Summary Section -->
        <div class="px-8 py-6 bg-gray-50 border-b border-gray-200 print:px-12">
          <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Summary</h3>
          <div class="grid grid-cols-2 gap-6">
            <div class="border-l-4 border-blue-600 pl-4">
              <p class="text-xs text-gray-600 uppercase tracking-wide">Total Parkout Tickets</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalTickets }}</p>
            </div>
            <div class="border-l-4 border-green-600 pl-4">
              <p class="text-xs text-gray-600 uppercase tracking-wide">Total Collection</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ formatCurrency(totalParkFee) }}</p>
            </div>
          </div>
        </div>

        <!-- Data Table -->
        <div class="px-8 py-6 print:px-12">
          <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
              <thead>
                <tr class="border-b-2 border-gray-300">
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Ticket No.
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Plate No.
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Park In
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Park Out
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Duration
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Payment
                  </th>
                  <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Amount
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="(ticket, index) in tickets" 
                  :key="ticket.id"
                  class="border-b border-gray-200 hover:bg-gray-50 print:hover:bg-white"
                  :class="{ 'bg-gray-50': index % 2 === 1 }"
                >
                  <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                    {{ ticket.ticket_no }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700">
                    {{ ticket.plate_no || 'N/A' }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700">
                    {{ formatDateTime(ticket.park_datetime) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700">
                    {{ formatDateTime(ticket.park_out_datetime) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700">
                    {{ formatDuration(ticket) }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700">
                    {{ ticket.mode_of_payment || 'Cash' }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                    ₱{{ formatCurrency(ticket.park_fee) }}
                  </td>
                </tr>
                <tr v-if="tickets.length === 0">
                  <td colspan="7" class="px-4 py-12 text-center text-gray-500 text-sm">
                    No parkout records found for today
                  </td>
                </tr>
              </tbody>
              <tfoot v-if="tickets.length > 0">
                <tr class="border-t-2 border-gray-800 bg-gray-100">
                  <td colspan="6" class="px-4 py-4 text-right text-sm font-bold text-gray-900 uppercase">
                    Total Amount:
                  </td>
                  <td class="px-4 py-4 text-right text-lg font-bold text-gray-900">
                    ₱{{ formatCurrency(totalParkFee) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- Report Footer -->
        <div class="px-8 py-6 border-t border-gray-200 print:px-12">
          <div class="grid grid-cols-3 gap-8 mt-8">
            <div class="text-center">
              <div class="border-t-2 border-gray-800 pt-2 mt-12">
                <p class="text-sm font-semibold text-gray-900">Prepared By</p>
                <p class="text-xs text-gray-600 mt-1">Cashier / Staff</p>
              </div>
            </div>
            <div class="text-center">
              <div class="border-t-2 border-gray-800 pt-2 mt-12">
                <p class="text-sm font-semibold text-gray-900">Verified By</p>
                <p class="text-xs text-gray-600 mt-1">Supervisor</p>
              </div>
            </div>
            <div class="text-center">
              <div class="border-t-2 border-gray-800 pt-2 mt-12">
                <p class="text-sm font-semibold text-gray-900">Approved By</p>
                <p class="text-xs text-gray-600 mt-1">Manager</p>
              </div>
            </div>
          </div>
          <div class="text-center text-xs text-gray-500 mt-8 pt-4 border-t border-gray-200">
            <p>This is a system-generated report</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
const props = defineProps({
  tickets: Array,
  totalParkFee: Number,
  totalTickets: Number,
  reportDate: String
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0);
};

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A';
  const date = new Date(datetime);
  return date.toLocaleString('en-PH', {
    month: 'short',
    day: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  });
};

const formatDuration = (ticket) => {
  const days = ticket.days_parked || 0;
  const hours = ticket.hours_parked || 0;
  const parts = [];
  
  if (days > 0) parts.push(`${days}d`);
  if (hours > 0) parts.push(`${hours}h`);
  
  return parts.length > 0 ? parts.join(' ') : '< 1h'; 
};

const printReport = () => {
  window.print();
};


const downloadPDF = () => {
  window.location.href = route('reports.parkout.download');
};

const exportToExcel = () => {
  let csv = 'Ticket No,Plate No,Park In,Park Out,Duration,Payment Method,Park Fee\n';
  
  props.tickets.forEach(ticket => {
    const parkIn = ticket.park_datetime ? new Date(ticket.park_datetime).toLocaleString('en-PH') : 'N/A';
    const parkOut = ticket.park_out_datetime ? new Date(ticket.park_out_datetime).toLocaleString('en-PH') : 'N/A';
    const duration = formatDuration(ticket);
    
    csv += `"${ticket.ticket_no}","${ticket.plate_no || 'N/A'}","${parkIn}","${parkOut}","${duration}","${ticket.mode_of_payment || 'Cash'}","${ticket.park_fee}"\n`;
  });
  
  csv += `\n,,,,,TOTAL:,${props.totalParkFee}`;
  
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);
  
  link.setAttribute('href', url);
  link.setAttribute('download', `parkout-report-${new Date().toISOString().split('T')[0]}.csv`);
  link.style.visibility = 'hidden';
  
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};
</script>

<style>
@media print {
  @page {
    size: landscape;
    margin: 0.5cm;
  }
  
  body {
    print-color-adjust: exact;
    -webkit-print-color-adjust: exact;
  }
  
  .print\:hidden {
    display: none !important;
  }
  
  .print\:block {
    display: block !important;
  }
  
  .print\:bg-white {
    background-color: white !important;
  }
  
  .print\:py-0 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }
  
  .print\:px-0 {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  .print\:px-12 {
    padding-left: 3rem !important;
    padding-right: 3rem !important;
  }
  
  .print\:py-8 {
    padding-top: 2rem !important;
    padding-bottom: 2rem !important;
  }
  
  .print\:shadow-none {
    box-shadow: none !important;
  }
  
  .print\:hover\:bg-white:hover {
    background-color: white !important;
  }
}
</style>