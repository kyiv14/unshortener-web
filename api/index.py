from flask import Flask, request, jsonify
import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse

app = Flask(__name__)

def get_site_metadata(response):
    """Извлекает заголовок, описание и фавиконку сайта"""
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # 1. Получаем заголовок
    title = soup.title.string if soup.title else "Заголовок не найден"
    
    # 2. Получаем описание (из обычного meta или OpenGraph)
    description = "Описание отсутствует"
    meta_desc = soup.find("meta", attrs={"name": "description"}) or \
                soup.find("meta", attrs={"property": "og:description"})
    if meta_desc:
        description = meta_desc.get("content", description)

    # 3. Получаем иконку сайта (favicon)
    favicon = "/favicon.ico"
    icon_link = soup.find("link", rel=lambda x: x and 'icon' in x.lower())
    if icon_link:
        favicon = urljoin(response.url, icon_link.get("href"))
    else:
        # Стандартный путь, если тег не найден
        parsed_uri = urlparse(response.url)
        favicon = f"{parsed_uri.scheme}://{parsed_uri.netloc}/favicon.ico"

    return {
        "title": title.strip(),
        "description": description.strip(),
        "favicon": favicon
    }

@app.route('/api/unshorten', methods=['POST'])
def unshorten():
    data = request.get_json()
    if not data or 'url' not in data:
        return jsonify({"status": "error", "message": "URL не предоставлен"}), 400

    target_url = data.get('url')
    
    # Добавляем http://, если пользователь ввел адрес без протокола
    if not target_url.startswith(('http://', 'https://')):
        target_url = 'http://' + target_url

    try:
        # Имитируем реальный браузер, чтобы избежать блокировок
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.5'
        }

        # Выполняем запрос с отслеживанием всех редиректов
        # timeout=10 защищает сервер от зависания на "медленных" ссылках
        response = requests.get(target_url, headers=headers, timeout=10, allow_redirects=True)
        
        # Собираем цепочку перенаправлений
        chain = [res.url for res in response.history]
        chain.append(response.url)

        # Получаем информацию о финальной странице
        meta = get_site_metadata(response)

        return jsonify({
            "status": "success",
            "final_url": response.url,
            "redirect_count": len(response.history),
            "chain": chain,
            "metadata": meta
        })

    except requests.exceptions.Timeout:
        return jsonify({"status": "error", "message": "Время ожидания истекло (Timeout)"}), 408
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

# Обязательная строка для Vercel
app = app
