<div>
    @foreach($notifications as $notification)
        <div class="notification-item">
            <p>{{ $notification->data['message'] }}</p>
            <p>Ruta del archivo: <a href="{{ $notification->data['file_path'] }}" target="_blank">{{ $notification->data['file_path'] }}</a></p>
        </div>
    @endforeach
</div>
