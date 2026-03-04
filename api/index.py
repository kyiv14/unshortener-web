from flask import Flask, request, jsonify
import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse

app = Flask(__name__)

def get_metadata(response):
    """Извлекает заголовок, описание и иконку (favicon) сайта"""
    try:
        soup = BeautifulSoup(response.text, 'html.parser')
        
        # 1. Заголовок
        title = soup.title.string if soup.title else "Заголовок не найден"
        
        # 2. Описание
        description = "Описание не найдено"
        meta_desc = soup.find("meta", attrs={"name": "description"}) or \
                    soup.find("meta", attrs={"property": "og:description"})
        if meta_desc:
            description = meta_desc.get("content", description)

        # 3. Фавиконка
        favicon = ""
        icon_link = soup.find("link", rel=lambda x: x and 'icon' in x.lower())
        if icon_link:
            favicon = urljoin(response.url, icon_link.get("href"))
        else:
            parsed_uri = urlparse(response.url)
            favicon = f"{parsed_uri.scheme}://{parsed_uri.netloc}/favicon.ico"

        return {
            "title": title.strip(),
            "description": description.strip(),
            "favicon": favicon
        }
    except:
        return {"title": "N/A", "description": "N/A", "favicon": ""}

@app.route('/api/unshorten', methods=['POST'])
def unshorten():
    data = request.get_json()
    if not data or 'url' not in data:
        return jsonify({"status": "error", "message": "URL не указан"}), 400

    url = data.get('url')
    
    # Добавляем протокол, если его нет
    if not url.startswith(('http://', 'https://')):
        url = 'https://' + url

    try:
        # Заголовки, чтобы сайты не блокировали "бота"
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
        }

        # Выполняем запрос с отслеживанием редиректов
        response = requests.get(url, headers=headers, timeout=10, allow_redirects=True)
        
        # Собираем историю всех перенаправлений
        history = [res.url for res in response.history]
        history.append(response.url)

        # Получаем данные о финальной странице
        metadata = get_metadata(response)

        return jsonify({
            "status": "success",
            "final_url": response.url,
            "redirect_count": len(response.history),
            "chain": history,
            "metadata": metadata
        })

    except requests.exceptions.Timeout:
        return jsonify({"status": "error", "message": "Время ожидания истекло"}), 408
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

# Это критически важно для Vercel
app = app
