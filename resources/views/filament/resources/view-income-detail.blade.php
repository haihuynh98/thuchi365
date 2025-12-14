<div class="space-y-6">
    {{-- Section 1: Thông tin Thu --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="bg-success-50 border-b border-success-200 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900">Thông tin Thu</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="py-3 px-4 text-right font-medium text-gray-700 w-1/2">Nhân viên:</td>
                            <td class="py-3 px-4 text-left text-gray-900">{{ $record->employee->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-right font-medium text-gray-700">Ngày ghi nhận:</td>
                            <td class="py-3 px-4 text-left text-gray-900">{{ $record->recorded_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-right font-medium text-gray-700">Doanh thu (vé):</td>
                            <td class="py-3 px-4 text-left text-gray-900">{{ number_format($record->revenue, 0, ',', '.') }} ₫</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-right font-medium text-gray-700">Tiền tip:</td>
                            <td class="py-3 px-4 text-left text-gray-900">{{ number_format($record->tip, 0, ',', '.') }} ₫</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-right font-medium text-gray-700">Tiền phạt:</td>
                            <td class="py-3 px-4 text-left text-gray-900">{{ number_format($record->penalty, 0, ',', '.') }} ₫</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-right font-medium text-gray-700">Cơ sở vật chất:</td>
                            <td class="py-3 px-4 text-left text-gray-900">{{ number_format($record->facility, 0, ',', '.') }} ₫</td>
                        </tr>
                        @if($record->note)
                            <tr>
                                <td class="py-3 px-4 text-right font-medium text-gray-700">Ghi chú:</td>
                                <td class="py-3 px-4 text-left text-gray-900">{{ $record->note }}</td>
                            </tr>
                        @endif
                        <tr class="bg-success-50 font-bold">
                            <td class="py-3 px-4 text-right text-gray-900">Tổng thu:</td>
                            <td class="py-3 px-4 text-left text-success-600 text-lg">{{ number_format($record->total, 0, ',', '.') }} ₫</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Section 2: Lịch sử thay đổi --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="bg-primary-50 border-b border-primary-200 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900">Lịch sử thay đổi</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">STT</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nội dung thay đổi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Người thay đổi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ngày thay đổi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $index => $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="space-y-1">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                                            @if($log->action === 'created') bg-success-100 text-success-700
                                            @elseif($log->action === 'updated') bg-warning-100 text-warning-700
                                            @else bg-danger-100 text-danger-700
                                            @endif">
                                            @if($log->action === 'created') Tạo mới
                                            @elseif($log->action === 'updated') Cập nhật
                                            @else Xóa
                                            @endif
                                        </span>
                                        @if($log->action === 'updated' && $log->old_values && $log->new_values)
                                            <div class="mt-2 space-y-1">
                                                @foreach($log->new_values as $key => $newValue)
                                                    @php
                                                        $oldValue = $log->old_values[$key] ?? null;
                                                    @endphp
                                                    @if($oldValue != $newValue)
                                                        <div class="text-xs text-gray-600">
                                                            <span class="font-medium">{{ $key }}:</span>
                                                            <span class="text-danger-600 line-through">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</span>
                                                            <span> → </span>
                                                            <span class="text-success-600">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $log->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                    Không có lịch sử thay đổi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
