<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Monitor Antrian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            margin: 0;
            overflow: hidden;
            color: white;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-glow {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
        .pulse {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="flex flex-col h-full p-8 space-y-8">
        <!-- Header -->
        <div class="w-full glass rounded-3xl p-6 flex justify-between items-center shadow-2xl">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-600 p-3 rounded-2xl shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black uppercase tracking-wider">Antrian Pintar</h1>
                    <p class="text-blue-400 font-semibold tracking-widest text-sm uppercase">Monitoring Real-time</p>
                </div>
            </div>
            <div class="text-right">
                <div id="current-time" class="text-4xl font-bold">00:00:00</div>
                <div id="current-date" class="text-gray-400 font-medium">Senin, 01 Januari 2026</div>
            </div>
        </div>

        <div class="flex-grow grid grid-cols-12 gap-8">
            <!-- Main Display Area -->
            <div class="col-span-8 glass rounded-[3rem] overflow-hidden flex flex-col items-center justify-center relative">
                <div class="absolute top-10 left-10 text-gray-500 font-black text-xl uppercase tracking-widest">Nomor Antrian Sekarang</div>
                
                <div id="display-container" class="transition-all duration-500">
                    <div id="nomor-antrian" class="text-[18rem] font-black text-white leading-none text-glow">
                        ---
                    </div>
                </div>

                <div id="loket-container" class="mt-8 bg-blue-600 px-12 py-4 rounded-full shadow-2xl pulse">
                    <span id="label-loket" class="text-4xl font-bold uppercase tracking-widest">Menuju LOKET 1</span>
                </div>
            </div>

            <!-- Side Cards / History -->
            <div class="col-span-4 flex flex-col space-y-8">
                <div class="flex-grow glass rounded-[3rem] p-8 flex flex-col">
                    <h2 class="text-2xl font-black uppercase tracking-widest mb-6 border-b border-white/10 pb-4">Info Layanan</h2>
                    <div class="space-y-6">
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/10">
                            <div class="text-blue-400 text-sm font-bold uppercase mb-1">Status Sistem</div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-xl font-bold">Terhubung (Reverb)</span>
                            </div>
                        </div>
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/10">
                            <div class="text-blue-400 text-sm font-bold uppercase mb-1">Pesan Suara</div>
                            <div id="tts-status" class="text-xl font-bold">Aktif & Siap</div>
                        </div>
                    </div>
                    
                    <!-- Overlay Start Button (Required for TTS Permission) -->
                    <div id="overlay-start" class="mt-auto bg-gradient-to-r from-blue-600 to-indigo-600 p-8 rounded-[2rem] text-center cursor-pointer hover:scale-[1.02] transition-transform active:scale-95 shadow-xl">
                        <p class="font-bold text-xl">Klik di Sini untuk Mengaktifkan Suara</p>
                        <p class="text-sm opacity-70 mt-1">WAJIB SETIAP KALI HALAMAN DIMUAT ULANG</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WebSocket & TTS Logic -->
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            // Clock Logic
            setInterval(() => {
                const now = new Date();
                document.getElementById('current-time').innerText = now.toLocaleTimeString('id-ID');
                document.getElementById('current-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }, 1000);

            // Handle Interaction to unlock TTS
            const overlay = document.getElementById('overlay-start');
            let ttsEnabled = false;

            overlay.addEventListener('click', () => {
                ttsEnabled = true;
                overlay.classList.add('hidden');
                speak("Sistem suara diaktifkan. Siap melayani.");
                document.getElementById('tts-status').innerText = "Aktif & Berjalan";
            });

            // WebSocket Listening
            if (window.Echo) {
                console.log('Echo terdeteksi, mencoba mendengarkan channel: layar-antrian');
                
                window.Echo.channel('layar-antrian')
                    .subscribed(() => {
                        console.log('BERHASIL terhubung ke channel: layar-antrian');
                        document.getElementById('tts-status').innerText = "Terhubung (Ready)";
                    })
                    .listen('.PanggilAntrian', (e) => {
                        console.log('EVENT DITERIMA:', e);
                        
                        // Update UI with animation
                        const nomorEl = document.getElementById('nomor-antrian');
                        const loketEl = document.getElementById('label-loket');
                        
                        nomorEl.style.opacity = '0';
                        setTimeout(() => {
                            nomorEl.innerText = e.nomor_antrian;
                            // Jika e.loket sudah mengandung kata 'Loket', gunakan apa adanya
                            const displayLoket = e.loket.toLowerCase().includes('loket') ? e.loket : "Loket " + e.loket;
                            loketEl.innerText = "Menuju " + displayLoket.toUpperCase();
                            nomorEl.style.opacity = '1';
                            
                            // Audio Panggilan
                            if (ttsEnabled) {
                                panggilSuara(e.nomor_antrian, e.loket);
                            }
                        }, 300);
                    })
                    .error((error) => {
                        console.error('Echo Error:', error);
                        document.getElementById('tts-status').innerText = "Koneksi Bermasalah";
                    });
            } else {
                console.error('Echo TIDAK ditemukan! Pastikan app.js sudah ter-load.');
            }

            function panggilSuara(nomor, loket) {
                // Split nomor misal A-001 menjadi A dan 1
                const parts = nomor.split('-');
                const prefix = parts[0];
                const cleanNomor = parseInt(parts[1]);
                
                const teks = `Nomor Antrian... ${prefix}... ${cleanNomor}... Menuju Loket... ${loket}`;
                speak(teks);
            }

            function speak(text) {
                const msg = new SpeechSynthesisUtterance();
                msg.text = text;
                msg.lang = 'id-ID';
                msg.rate = 0.8;
                msg.pitch = 1;
                window.speechSynthesis.speak(msg);
            }
        });
    </script>
</body>
</html>
