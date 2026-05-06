@extends('layouts.app')

@section('title', 'Bank Transfer Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-university text-4xl text-blue-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Bank Transfer Payment</h1>
                <p class="text-gray-400 mt-2">Transfer to one of our bank accounts below</p>
            </div>

            <!-- Bank Accounts -->
            <div class="space-y-4 mb-6">
                @foreach($bankAccounts ?? [
                    ['bank' => 'BCA', 'account_number' => '123 4567 890', 'account_name' => 'PT Hotel Bahagia'],
                    ['bank' => 'Mandiri', 'account_number' => '7890 1234 5678', 'account_name' => 'PT Hotel Bahagia'],
                    ['bank' => 'BRI', 'account_number' => '5678 9012 3456', 'account_name' => 'PT Hotel Bahagia']
                ] as $bank)
                <div class="bg-white/5 rounded-xl p-4 border border-blue-500/20">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-white font-bold text-lg">{{ $bank['bank'] }}</span>
                        <i class="fas fa-building text-gray-500"></i>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Account Number:</span>
                        <div class="flex items-center gap-2">
                            <span class="text-white font-mono">{{ $bank['account_number'] }}</span>
                            <button onclick="copyText('{{ $bank['account_number'] }}')" class="text-yellow-500 hover:text-yellow-400">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-gray-400">Account Name:</span>
                        <span class="text-white">{{ $bank['account_name'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Form Upload Bukti -->
            <form action="{{ route('payment.transfer.process', $booking->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-white mb-2">Choose Bank</label>
                    <select name="bank_name" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white" required>
                        <option value="">Select Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Bank Mandiri</option>
                        <option value="BRI">Bank BRI</option>
                        <option value="BNI">Bank BNI</option>
                        <option value="CIMB">CIMB Niaga</option>
                        <option value="Permata">Bank Permata</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-white mb-2">Your Account Number</label>
                    <input type="text" name="account_number" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white" placeholder="e.g., 1234567890" required>
                </div>

                <div class="mb-4">
                    <label class="block text-white mb-2">Transfer Date</label>
                    <input type="date" name="transfer_date" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white" required>
                </div>

                <div class="mb-4">
                    <label class="block text-white mb-2">Transfer Amount</label>
                    <input type="number" name="transfer_amount" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white" placeholder="Total amount transferred" value="{{ $booking->total_price }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-white mb-2">Upload Payment Proof</label>
                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-yellow-500 transition" onclick="document.getElementById('proof').click()">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-500 mb-2"></i>
                        <p class="text-gray-400 text-sm">Click to upload transfer receipt / screenshot</p>
                        <p class="text-gray-500 text-xs">JPG, PNG, JPEG, PDF (Max 2MB)</p>
                    </div>
                    <input type="file" name="payment_proof" id="proof" class="hidden" accept="image/*,application/pdf" required>
                    <p id="fileName" class="text-gray-500 text-sm mt-1"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-white mb-2">Notes (Optional)</label>
                    <textarea name="note" rows="2" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white" placeholder="e.g., Transfer from BCA a/n John Doe"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-upload mr-2"></i> Upload & Confirm Payment
                </button>
            </form>

            <p class="text-center text-gray-500 text-xs mt-4">
                Payment will be verified by admin within 1x24 hours
            </p>
        </div>
    </div>
</div>

<script>
    function copyText(text) {
        navigator.clipboard.writeText(text.replace(/\s/g, ''));
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Account number copied!',
            timer: 1500,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff'
        });
    }

    document.getElementById('proof')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            document.getElementById('fileName').innerHTML = '<i class="fas fa-check-circle text-green-500"></i> ' + fileName;
        }
    });
</script>
@endsection