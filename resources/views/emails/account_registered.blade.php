<x-mail::message>
    @component('mail::message')
    # 👗 Welcome to DressUp Davao, {{ $userName }}!

    <div style="text-align:center; margin:20px 0;">
        <img src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao Logo" width="120"
            style="margin-bottom:15px;">
    </div>

    <p style="font-size:16px;">
        Thank you for joining <strong>DressUp Davao</strong>! We’re excited to help you find your perfect look.
    </p>

    <ul>
        <li>✨ Browse our latest gowns and suits</li>
        <li>🧵 Save your measurements for quick checkout</li>
        <li>🎀 Receive updates about special offers</li>
    </ul>

    @component('mail::button', ['url' => url('/')])
    Explore the Collection
    @endcomponent

    <p>Warm regards,<br>
        The <strong>DressUp Davao</strong> Team</p>
    @endcomponent
</x-mail::message>