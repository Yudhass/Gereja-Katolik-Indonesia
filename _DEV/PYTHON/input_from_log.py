from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
import os, time, re, json, openpyxl

BASE_URL = "http://192.168.1.240/Gereja-Katolik-Indonesia"
# BASE_URL = "http://192.168.1.4/Gereja-Katolik-Indonesia"
EMAIL = "admin.gereja.katolik.indonesia@gmail.com"
PASSWORD = "Admin123_@"

LOG_PATH = os.path.join(os.path.dirname(__file__), "log.txt")
EXCEL_PATH = os.path.join(os.path.dirname(__file__), "data_matang", "Gereja-Katolik.xlsx")

PROVINSI_LIST = [
    "ACEH",
    "SUMATERA UTARA",
    "SUMATERA BARAT",
    "RIAU",
    "JAMBI",
    "SUMATERA SELATAN",
    "BENGKULU",
    "LAMPUNG",
    "KEPULAUAN BANGKA BELITUNG",
    "KEPULAUAN RIAU",
    "DKI JAKARTA",
    "JAWA BARAT",
    "JAWA TENGAH",
    "DAERAH ISTIMEWA YOGYAKARTA",
    "JAWA TIMUR",
    "BANTEN",
    "BALI",
    "NUSA TENGGARA BARAT",
    "NUSA TENGGARA TIMUR",
    "KALIMANTAN BARAT",
    "KALIMANTAN TENGAH",
    "KALIMANTAN SELATAN",
    "KALIMANTAN TIMUR",
    "KALIMANTAN UTARA",
    "SULAWESI UTARA",
    "SULAWESI TENGAH",
    "SULAWESI SELATAN",
    "SULAWESI TENGGARA",
    "GORONTALO",
    "SULAWESI BARAT",
    "MALUKU",
    "MALUKU UTARA",
    "PAPUA",
    "PAPUA BARAT",
    "PAPUA SELATAN",
    "PAPUA TENGAH",
    "PAPUA PEGUNUNGAN"
]

def load_log():
    if not os.path.exists(LOG_PATH):
        print(f"Log file tidak ditemukan: {LOG_PATH}")
        return []
    
    entries = []
    with open(LOG_PATH, "r", encoding="utf-8") as f:
        for line in f:
            line = line.strip()
            if line:
                try:
                    entries.append(json.loads(line))
                except json.JSONDecodeError:
                    continue
    return entries

def load_excel():
    if not os.path.exists(EXCEL_PATH):
        print(f"Excel file tidak ditemukan: {EXCEL_PATH}")
        return []
    
    entries = []
    wb = openpyxl.load_workbook(EXCEL_PATH)
    ws = wb.active
    
    headers = [cell.value for cell in ws[1]]
    
    for row in ws.iter_rows(min_row=2, values_only=True):
        entry = {}
        for i, val in enumerate(row):
            if i < len(headers) and headers[i]:
                entry[str(headers[i]).lower().strip()] = val
        if entry:
            entries.append(entry)
    
    wb.close()
    return entries

def select2_set(driver, select_id, value):
    sel = driver.find_element(By.ID, select_id)
    opts = [(o.get_attribute("value"), o.text) for o in sel.find_elements(By.TAG_NAME, "option")]
    found_val = None
    found_text = None
    for val, txt in opts:
        if txt.upper().strip() == value.upper().strip() or val.upper().strip() == value.upper().strip():
            found_val, found_text = val, txt
            break
    if not found_val:
        for val, txt in opts:
            if value.upper() in txt.upper() or value.upper() in val.upper():
                found_val, found_text = val, txt
                break
    if not found_val:
        print(f"   Debug {select_id}: {opts}")
        return False

    try:
        selection = driver.find_element(By.CSS_SELECTOR, f"span[aria-labelledby='select2-{select_id}-container']")
        selection.click()
        time.sleep(0.1)
        try:
            search = driver.find_element(By.CSS_SELECTOR, "input.select2-search__field")
            search.clear()
            search.send_keys(found_text)
            time.sleep(0.1)
        except:
            pass
        option = WebDriverWait(driver, 3).until(
            EC.element_to_be_clickable((By.XPATH, f"//li[contains(@class,'select2-results__option')][normalize-space()='{found_text}']"))
        )
        option.click()
        return True
    except:
        pass

    try:
        Select(sel).select_by_value(found_val)
        driver.execute_script("arguments[0].dispatchEvent(new Event('change', {bubbles: true}));", sel)
        try:
            driver.execute_script("jQuery(arguments[0]).trigger('change.select2');", sel)
        except:
            pass
        return True
    except:
        return False

def login(driver):
    driver.get(f"{BASE_URL}/login")
    time.sleep(2)
    try:
        email_input = driver.find_element(By.NAME, "email")
        password_input = driver.find_element(By.NAME, "password")
        email_input.clear()
        email_input.send_keys(EMAIL)
        password_input.clear()
        password_input.send_keys(PASSWORD)
        password_input.submit()
        time.sleep(3)
        print("Login berhasil")
        return True
    except Exception as e:
        print(f"Login gagal: {e}")
        return False

def input_gereja():
    entries = load_log()
    source = "log.txt"
    
    if not entries:
        print("Tidak ada data di log.txt, membaca dari Excel...")
        entries = load_excel()
        source = "Excel"
    
    if not entries:
        print("Tidak ada data di log.txt maupun Excel")
        return
    
    print(f"Memproses {len(entries)} entri dari {source}")
    
    try:
        options = webdriver.ChromeOptions()
        options.add_argument("--start-maximized")
        options.binary_location = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
        service = Service(ChromeDriverManager().install())
        driver = webdriver.Chrome(service=service, options=options)
    except Exception as e:
        print(f"\nGagal membuka Chrome: {e}")
        print("Install ChromeDriver dengan: pip install webdriver-manager")
        print("Atau download manual dari: https://chromedriver.chromium.org/downloads")
        return
    
    try:
        if not login(driver):
            return

        for idx, entry in enumerate(entries, 1):
            print(f"\nMemproses entri {idx}/{len(entries)}: {entry.get('nama_gereja', 'Unknown')}")
            
            driver.get(f"{BASE_URL}/admin/gereja/create")
            time.sleep(2)
            
            try:
                def get_val(*keys):
                    for k in keys:
                        v = entry.get(k, "")
                        if v:
                            return v
                    return ""
                
                fields = {
                    "nama_gereja": get_val("nama_gereja", "nama", "gereja"),
                    "alamat": get_val("alamat", "address"),
                    "kelurahan": get_val("kelurahan", "desa", "kel"),
                    "kecamatan": get_val("kecamatan", "kec", "district"),
                    "kabupaten": get_val("kabupaten", "kota", "kab", "regency"),
                    "provinsi": get_val("provinsi", "prov", "province"),
                    "latitude": get_val("latitude", "lat"),
                    "longitude": get_val("longitude", "lng", "lon", "long"),
                }
                
                for field_id, value in fields.items():
                    if value:
                        try:
                            if field_id in ["provinsi", "kabupaten", "kecamatan"]:
                                select2_set(driver, field_id, value)
                            else:
                                el = driver.find_element(By.ID, field_id)
                                el.clear()
                                el.send_keys(str(value))
                        except Exception as e:
                            print(f"   Warning: Gagal mengisi {field_id}: {e}")
                
                time.sleep(1)
                
                submit_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit'], input[type='submit']")
                submit_btn.click()
                time.sleep(3)
                
                print(f"   Berhasil menginput: {entry.get('nama_gereja')}")
                
                with open(LOG_PATH, "a", encoding="utf-8") as f:
                    f.write(json.dumps(entry, ensure_ascii=False) + "\n")
                
            except Exception as e:
                print(f"   Error memproses entri: {e}")
                
    finally:
        driver.quit()
        print("\nSelesai")

if __name__ == "__main__":
    input_gereja()
