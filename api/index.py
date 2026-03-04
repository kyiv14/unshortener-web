from flask import Flask, request, jsonify
import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse

app = Flask(__name__)

def get_metadata(response):
    """Извлекает заголовок, описание и иконку (favicon) финального сайта"""
    try:
        soup = BeautifulSoup(response.text, 'html.parser')
        
        # 1. Заголовок сайта
        title = soup.title.string if soup.title else "Заголовок не найден"
        
        # 2. Описание сайта (из мета-тегов)
        description = "Описание не найдено"
        meta_desc = soup.find("meta", attrs={"name": "description"}) or \
                    soup.find("meta", attrs={"property": "og:description"})
        if meta_desc:
            description = meta_desc.get("content", description)

        # 3. Фавиконка (иконка сайта)
        favicon = ""
        icon_link = soup.find("link", rel=lambda x: x and 'icon' in x.lower())
        if icon_link:
            favicon = urljoin(response.url, icon_link.get("href"))
        else:
            # Если тег не найден, пробуем стандартный путь
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
    # Получаем JSON данные из запроса
    data = request.get_json()
    if not data or 'url' not in data:
        return jsonify({"status": "error", "message": "Введите URL"}), 400

    url = data.get('url')
    
    # Автоматически добавляем протокол, если пользователь его забыл
    if not url.startswith(('http://', 'https://')):
        url = 'https://' + url

    try:
        # Заголовки, чтобы выглядеть как обычный Chrome на Windows
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
        }

        # Выполняем запрос с отслеживанием всех редиректов (allow_redirects=True)
        # timeout=10 нужен, чтобы сервер не зависал на битых ссылках
        response = requests.get(url, headers=headers, timeout=10, allow_redirects=True)
        
        # Собираем историю всех прыжков (редиректов)
        history = [res.url for res in response.history]
        history.append(response.url) # Добавляем финальную точку

        # Получаем данные о сайте для красивого отображения
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

# Это критически важно для корректного деплоя на Vercel
app = app
