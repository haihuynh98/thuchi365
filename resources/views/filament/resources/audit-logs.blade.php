<div class="space-y-4">
    @forelse($logs as $log)
        <div class="border rounded-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded
                        @if($log->action === 'created') bg-success-100 text-success-700
                        @elseif($log->action === 'updated') bg-warning-100 text-warning-700
                        @else bg-danger-100 text-danger-700
                        @endif">
                        @if($log->action === 'created') Tạo mới
                        @elseif($log->action === 'updated') Cập nhật
                        @else Xóa
                        @endif
                    </span>
                    <span class="text-sm text-gray-600">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </span>
                </div>
                <span class="text-sm text-gray-500">
                    {{ $log->user->name ?? 'N/A' }}
                </span>
            </div>
            
            @if($log->action === 'updated' && $log->old_values && $log->new_values)
                <div class="mt-2 space-y-1 text-sm">
                    @foreach($log->new_values as $key => $newValue)
                        @php
                            $oldValue = $log->old_values[$key] ?? null;
                        @endphp
                        @if($oldValue != $newValue)
                            <div class="flex gap-2">
                                <span class="font-medium text-gray-700">{{ $key }}:</span>
                                <span class="text-danger-600 line-through">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</span>
                                <span>→</span>
                                <span class="text-success-600">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
            
            @if($log->ip_address)
                <div class="text-xs text-gray-400 mt-2">
                    IP: {{ $log->ip_address }}
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            Không có lịch sử thay đổi
        </div>
    @endforelse
</div>

