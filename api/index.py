from flask import Flask, request, jsonify
import requests
from bs4 import BeautifulSoup

app = Flask(__name__)

@app.route('/api/unshorten', methods=['POST'])
def unshorten():
    data = request.get_json()
    url = data.get('url')
    if not url:
        return jsonify({"error": "No URL"}), 400
    try:
        headers = {'User-Agent': 'Mozilla/5.0'}
        # Мы используем GET, чтобы BeautifulSoup мог прочитать Title
        response = requests.get(url, headers=headers, timeout=10, allow_redirects=True)
        soup = BeautifulSoup(response.text, 'html.parser')
        title = soup.title.string if soup.title else "Без заголовка"
        return jsonify({
            "final_url": response.url,
            "title": title.strip()
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500
      
