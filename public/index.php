<?php
session_start();

// 1. ЛОГИКА ПЕРЕКЛЮЧЕНИЯ ЯЗЫКОВ
$langs = [
    'ru' => [
        'title' => 'Link Unmask — Раскрой тайну ссылки',
        'hero_h1' => 'Раскрой <span>тайну</span> ссылки',
        'hero_sub' => 'Безопасный просмотр сокращенных URL без риска для данных',
        'placeholder' => 'Вставьте ссылку (bit.ly, t.co...)',
        'btn_text' => 'Раскрыть сейчас',
        'btn_loading' => 'Раскрываем...',
        'result_head' => 'Результат анализа:',
        'result_target' => 'Конечная цель:',
        'result_chain' => 'Цепочка переходов:',
        'error_msg' => 'Не удалось проверить ссылку'
    ],
    'uk' => [
        'title' => 'Link Unmask — Розкрий таємницю посилання',
        'hero_h1' => 'Розкрий <span>таємницю</span> посилання',
        'hero_sub' => 'Безпечний перегляд скорочених URL без ризику для даних',
        'placeholder' => 'Вставте посилання (bit.ly, t.co...)',
        'btn_text' => 'Розкрити зараз',
        'btn_loading' => 'Розкриваємо...',
        'result_head' => 'Результат аналізу:',
        'result_target' => 'Кінцева мета:',
        'result_chain' => 'Ланцюжок переходів:',
        'error_msg' => 'Не вдалося перевірити посилання'
    ],
    'en' => [
        'title' => 'Link Unmask — Unreveal the link mystery',
        'hero_h1' => 'Unreveal the <span>link</span> mystery',
        'hero_sub' => 'Safe browsing of shortened URLs without data risk',
        'placeholder' => 'Paste link (bit.ly, t.co...)',
        'btn_text' => 'Unmask now',
        'btn_loading' => 'Unmasking...',
        'result_head' => 'Analysis result:',
        'result_target' => 'Final destination:',
        'result_chain' => 'Redirect chain:',
        'error_msg' => 'Failed to check the link'
    ],
    'pl' => [
        'title' => 'Link Unmask — Odkryj tajemnicę linku',
        'hero_h1' => 'Odkryj <span>tajemnicę</span> linku',
        'hero_sub' => 'Bezpieczne przeglądanie skróconych adresów URL',
        'placeholder' => 'Wklej link (bit.ly, t.co...)',
        'btn_text' => 'Odkryj teraz',
        'btn_loading' => 'Odkrywanie...',
        'result_head' => 'Wynik analizy:',
        'result_target' => 'Cel końcowy:',
        'result_chain' => 'Łańcuch przekierowań:',
        'error_msg' => 'Nie udało się sprawdzić linku'
    ]
];

$curr = $_GET['lang'] ?? $_SESSION['lang'] ?? 'ru';
if (!array_key_exists($curr, $langs)) $curr = 'ru';
$_SESSION['lang'] = $curr;
$t = $langs[$curr];
?>

<!DOCTYPE html>
<html lang="<?php echo $curr; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2280%22 fill=%22%23de2624%22>🛡️</text></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #ffffff;
            color: #1d1d1f;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding-top: 20px;
        }

        /* Блок переключения языков */
        .lang-switcher {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            background: #f5f5f7;
            padding: 8px 20px;
            border-radius: 50px;
        }
        .lang-link {
            font-size: 13px;
            font-weight: 700;
            color: #86868b;
            text-decoration: none;
            text-transform: uppercase;
            transition: color 0.3s;
        }
        .lang-link.active { color: #de2624; }
        .lang-link:hover { color: #1d1d1f; }

        .logo-text {
            font-size: 32px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: center;
            margin-bottom: 30px;
        }
        .logo-text span { color: #de2624; }
        
        .logo-icon-container {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-title {
            font-size: 40.8px;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 15px;
            text-align: center;
            letter-spacing: -1px;
        }
        .hero-title span { color: #de2624; }

        .hero-sub {
            color: #86868b;
            font-size: 19px;
            text-align: center;
            max-width: 500px;
            margin: 0 auto 25px;
            font-weight: 500;
        }

        .main-card {
            background: #fbfbfd;
            border-radius: 45px;
            padding: 40px 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.02);
            width: 100%;
        }

        .input-field {
            width: 100%;
            padding: 22px 28px;
            border-radius: 22px;
            border: 1px solid #e5e5e5;
            font-size: 17px;
            font-weight: 500;
            outline: none;
            background: #ffffff;
            margin-bottom: 20px;
            text-align: center;
        }

        .btn-black {
            background: #1d1d1f;
            color: #ffffff;
            width: 100%;
            padding: 22px;
            border-radius: 60px;
            font-weight: 800;
            font-size: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
        }

        .result-card {
            margin-top: 30px;
            padding: 30px;
            background: #ffffff;
            border-radius: 30px;
            border: 2px solid #de2624;
            display: none;
        }
    </style>
</head>
<body>

    <div class="lang-switcher">
        <a href="?lang=ru" class="lang-link <?php echo $curr=='ru'?'active':''; ?>">RU</a>
        <a href="?lang=uk" class="lang-link <?php echo $curr=='uk'?'active':''; ?>">UA</a>
        <a href="?lang=en" class="lang-link <?php echo $curr=='en'?'active':''; ?>">EN</a>
        <a href="?lang=pl" class="lang-link <?php echo $curr=='pl'?'active':''; ?>">PL</a>
    </div>

    <div class="max-w-lg w-full px-6">
        
        <div class="logo-text">
            <div class="logo-icon-container">
                <lottie-player 
                    src="wired-flat-11-link-unlink-hover-bounce.json" 
                    background="transparent" 
                    speed="1" 
                    style="width: 100px; height: 100px;" 
                    loop 
                    autoplay>
                </lottie-player>
            </div>
            Link <span>Unmask</span>
        </div>

        <h1 class="hero-title"><?php echo $t['hero_h1']; ?></h1>
        <p class="hero-sub"><?php echo $t['hero_sub']; ?></p>

        <div class="main-card">
            <input id="urlInput" type="text" placeholder="<?php echo $t['placeholder']; ?>" class="input-field">
            
            <button onclick="unshorten()" id="btn" class="btn-black">
                <span id="btnText"><?php echo $t['btn_text']; ?></span>
                <i id="loader" class="fas fa-circle-notch fa-spin hidden"></i>
            </button>
        </div>

        <div id="result" class="result-card">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-info-circle text-red-600"></i>
                <h3 class="text-xs font-black uppercase tracking-widest text-red-600"><?php echo $t['result_head']; ?></h3>
            </div>
            
            <div id="siteTitle" class="text-2xl font-black mb-2 text-slate-900 leading-tight"></div>
            
            <div class="bg-slate-50 p-4 rounded-2xl mb-6 border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2"><?php echo $t['result_target']; ?></p>
                <a id="finalUrl" href="#" target="_blank" class="text-blue-600 break-all text-sm font-bold underline"></a>
            </div>
            
            <div class="pt-5 border-t border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-widest"><?php echo $t['result_chain']; ?></p>
                <div id="redirectChain" class="space-y-1"></div>
            </div>
        </div>
    </div>

    <script>
        // Передаем тексты из PHP в JS
        const langData = {
            loading: "<?php echo $t['btn_loading']; ?>",
            normal: "<?php echo $t['btn_text']; ?>",
            error: "<?php echo $t['error_msg']; ?>"
        };

        async function unshorten() {
            const input = document.getElementById('urlInput');
            const btn = document.getElementById('btn');
            const btnText = document.getElementById('btnText');
            const loader = document.getElementById('loader');
            const resultBox = document.getElementById('result');
            
            const rawUrl = input.value.trim();
            if (!rawUrl) return;

            btn.disabled = true;
            btnText.innerText = langData.loading;
            loader.classList.remove('hidden');
            resultBox.style.display = 'none';

            try {
                const response = await fetch('/api/unshorten', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ url: rawUrl })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    document.getElementById('siteTitle').innerText = data.metadata.title || "---";
                    document.getElementById('finalUrl').innerText = data.final_url;
                    document.getElementById('finalUrl').href = data.final_url;
                    
                    const chainBox = document.getElementById('redirectChain');
                    chainBox.innerHTML = '';
                    data.chain.forEach((link) => {
                        chainBox.innerHTML += `
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-red-600 rounded-full flex-shrink-0"></div>
                                <span class="truncate text-[12px] font-bold text-slate-500">${link}</span>
                            </div>`;
                    });

                    resultBox.style.display = 'block';
                } else {
                    alert(langData.error);
                }
            } catch (e) {
                alert("Server Error");
            } finally {
                btn.disabled = false;
                btnText.innerText = langData.normal;
                loader.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
