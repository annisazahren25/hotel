@extends('layouts.app')

@section('content')
<div class="text-center mb-12">
    <h1 class="font-serif text-5xl gold-text mb-4">Luxury Suites</h1>
    <p class="text-gray-300 text-lg">Experience unparalleled comfort and elegance</p>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($roomTypes as $type)
    <div class="glass-card rounded-2xl overflow-hidden room-card">
        <div class="h-64 bg-cover bg-center" style="background-image: url('{{ $type->photo_url ?? 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800' }}')"></div>
        <div class="p-6">
            <h3 class="font-serif text-2xl gold-text mb-2">{{ $type->name }}</h3>
            <p class="text-gray-300 mb-4">{{ $type->description ?? 'Luxurious room with premium amenities' }}</p>
            <div class="flex justify-between items-center mb-4">
                <span class="text-2xl font-bold gold-text">Rp {{ number_format($type->price, 0, ',', '.') }}</span>
                <span class="text-sm text-gray-400">/night</span>
            </div>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-bed text-gold-500"></i>
                    <span class="text-sm">Capacity: {{ $type->capacity ?? 2 }} guests</span>
                </div>
                <button onclick="checkAvailability({{ $type->id }})" class="px-4 py-2 btn-gold text-black rounded-lg font-semibold text-sm">Book Now</button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
function checkAvailability(roomTypeId) {
    // Modal untuk pilih tanggal
    alert('Please select dates to check availability');
}
</script>
@endpush