<div>
    @foreach ($notifications as $notification)
        <div class="notification-card">
            <button class="notification-close" onclick="removeNotification(this, event)">&times;</button>
            <div class="notification-header">
                <p class="notification-date">{{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}</p>
            </div>
            <p class="notification-message">{{ $notification->data['message'] }}</p>
            <div class="notification-footer">
                <button class="notification-download" onclick="window.open('{{ $notification->data['file_path'] }}', '_blank')">Descargar</button>
            </div>
        </div>
    @endforeach
</div>
