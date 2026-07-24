from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
import openpyxl
import os, time, re, json

BASE_URL = "http://192.168.1.10/Gereja-Katolik-Indonesia"
EMAIL = "admin.gereja.katolik.indonesia@gmail.com"
PASSWORD = "Admin123_@"

EXCEL_PATH = os.path.join(os.path.dirname(__file__), "data_matang", "Gereja-Katolik.xlsx")

PROVINSI_LIST = [
    "ACEH", "BALI", "BANTEN", "BENGKULU", "DAERAH ISTIMEWA YOGYAKARTA", "DKI JAKARTA",
    "GORONTALO", "JAMBI", "JAWA BARAT", "JAWA TENGAH", "JAWA TIMUR", "KALIMANTAN BARAT",
    "KALIMANTAN SELATAN", "KALIMANTAN TENGAH", "KALIMANTAN TIMUR", "KALIMANTAN UTARA",
    "KEPULAUAN BANGKA BELITUNG", "KEPULAUAN RIAU", "LAMPUNG", "MALUKU", "MALUKU UTARA",
    "NUSA TENGGARA BARAT", "NUSA TENGGARA TIMUR", "PAPUA", "PAPUA BARAT", "PAPUA PEGUNUNGAN",
    "PAPUA SELATAN", "PAPUA TENGAH", "RIAU", "SULAWESI BARAT", "SULAWESI SELATAN",
    "SULAWESI TENGAH", "SULAWESI TENGGARA", "SULAWESI UTARA", "SUMATERA BARAT",
    "SUMATERA SELATAN", "SUMATERA UTARA"
]

def extract_wilayah(alamat):
    result = {"provinsi": "", "kabupaten": "", "kecamatan": "", "kelurahan": ""}
    if not alamat:
        return result

    parts = [p.strip() for p in alamat.split(",") if p.strip()]

    for idx in range(len(parts) - 1, -1, -1):
        part = parts[idx]
        for p in PROVINSI_LIST:
            if p in part.upper():
                result["provinsi"] = p
                parts.pop(idx)
                break
        if result["provinsi"]:
            break

    for idx in range(len(parts) - 1, -1, -1):
        part = parts[idx]
        m = re.match(r'(?:Kota|Kabupat[ae]n|Kab\.)\s+(.+)', part, re.IGNORECASE)
        if m:
            result["kabupaten"] = m.group(1).strip()
            parts.pop(idx)
            break

    if not result["kabupaten"] and parts:
        result["kabupaten"] = parts.pop()

    for idx in range(len(parts) - 1, -1, -1):
        part = parts[idx]
        m = re.match(r'(?:Kecamat[ae]n|Kec\.)\s+(.+)', part, re.IGNORECASE)
        if m:
            result["kecamatan"] = m.group(1).strip()
            parts.pop(idx)
            break

    if parts:
        last = parts[-1]
        last = re.sub(r'^(?:Kelurahan|Kel\.|Desa|Dusun)\s+', '', last, flags=re.IGNORECASE).strip()
        if last and len(last) < 60:
            result["kelurahan"] = last

    return result

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
        time.sleep(0.5)
        try:
            search = driver.find_element(By.CSS_SELECTOR, "input.select2-search__field")
            search.clear()
            search.send_keys(found_text)
            time.sleep(0.5)
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

def get_platform(website):
    if not website:
        return "website"
    w = website.lower()
    if "instagram.com" in w:
        return "instagram"
    if "facebook.com" in w or "fb.com" in w:
        return "facebook"
    if "twitter.com" in w or "x.com" in w:
        return "twitter"
    if "youtube.com" in w or "youtu.be" in w:
        return "youtube"
    if "tiktok.com" in w:
        return "tiktok"
    if "linkedin.com" in w:
        return "linkedin"
    if "wa.me" in w or "whatsapp" in w:
        return "whatsapp"
    if "t.me" in w or "telegram" in w:
        return "telegram"
    return "website"


wdm_dir = os.path.expanduser("~/.wdm/drivers/chromedriver/win64")
chromedriver_path = None
if os.path.exists(wdm_dir):
    for root, dirs, files in os.walk(wdm_dir):
        for f in files:
            if f == "chromedriver.exe":
                chromedriver_path = os.path.join(root, f)
                break

if chromedriver_path:
    print(f"ChromeDriver: {chromedriver_path}")
    service = Service(chromedriver_path)
else:
    from webdriver_manager.chrome import ChromeDriverManager
    service = Service(ChromeDriverManager().install())

driver = webdriver.Chrome(service=service)
driver.maximize_window()
wait = WebDriverWait(driver, 10)

driver.get(f"{BASE_URL}/login")
print("1. Halaman login terbuka")

wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(EMAIL)
driver.find_element(By.NAME, "password").send_keys(PASSWORD)
driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
print("2. Login berhasil")
time.sleep(2)

driver.get(f"{BASE_URL}/admin/gereja")
print("3. Halaman admin/gereja terbuka")
time.sleep(2)

wb = openpyxl.load_workbook(EXCEL_PATH, read_only=True, data_only=True)
ws = wb.active

for idx, row in enumerate(ws.iter_rows(min_row=2, values_only=True), start=1):
    name_val = row[1]
    address_val = row[18] if len(row) > 18 else None
    link_val = row[20] if len(row) > 20 else None
    phone_val = row[8] if len(row) > 8 else None
    website_val = row[7] if len(row) > 7 else None

    if not name_val:
        continue

    print(f"\n--- Data ke-{idx}: {name_val} ---")

    btn_tambah = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "button[data-bs-target='#modalAdd']")))
    driver.execute_script("arguments[0].click();", btn_tambah)
    print("4. Klik Tambah Gereja")
    time.sleep(1)

    wait.until(EC.presence_of_element_located((By.NAME, "nama_gereja"))).send_keys(str(name_val))
    print(f"5. Nama: {name_val}")

    if address_val:
        addr_str = str(address_val)
        driver.find_element(By.NAME, "alamat").send_keys(addr_str)
        print(f"   Alamat: {addr_str[:50]}...")

        wilayah = extract_wilayah(addr_str)
        print(f"   Wilayah: {wilayah}")

        if wilayah["provinsi"]:
            ok = select2_set(driver, "addProvinsi", wilayah["provinsi"])
            if ok:
                print(f"   Provinsi: {wilayah['provinsi']}")
                time.sleep(2)

                if wilayah["kabupaten"]:
                    try:
                        wait.until(lambda d: not d.find_element(By.ID, "addKabupaten").get_attribute("disabled"))
                        time.sleep(1)
                        ok2 = select2_set(driver, "addKabupaten", wilayah["kabupaten"])
                        if ok2:
                            print(f"   Kabupaten: {wilayah['kabupaten']}")
                            time.sleep(2)

                            if wilayah["kecamatan"]:
                                try:
                                    wait.until(lambda d: not d.find_element(By.ID, "addKecamatan").get_attribute("disabled"))
                                    time.sleep(1)
                                    ok3 = select2_set(driver, "addKecamatan", wilayah["kecamatan"])
                                    if ok3:
                                        print(f"   Kecamatan: {wilayah['kecamatan']}")
                                        time.sleep(2)

                                        if wilayah["kelurahan"]:
                                            try:
                                                wait.until(lambda d: not d.find_element(By.ID, "addKelurahan").get_attribute("disabled"))
                                                time.sleep(1)
                                                ok4 = select2_set(driver, "addKelurahan", wilayah["kelurahan"])
                                                if ok4:
                                                    print(f"   Kelurahan: {wilayah['kelurahan']}")
                                                    time.sleep(1)
                                            except:
                                                print(f"   Kelurahan tidak terpilih: {wilayah['kelurahan']}")
                                except:
                                    print(f"   Kecamatan tidak terpilih: {wilayah['kecamatan']}")
                    except:
                        print(f"   Kabupaten tidak terpilih: {wilayah['kabupaten']}")
            else:
                print(f"   Provinsi tidak ditemukan di dropdown: {wilayah['provinsi']}")

    if phone_val:
        driver.find_element(By.NAME, "kontak_telepon").send_keys(str(phone_val))
        print(f"   Telepon: {phone_val}")

    if link_val:
        original_window = driver.current_window_handle
        driver.execute_script("window.open(arguments[0]);", str(link_val))
        driver.switch_to.window(driver.window_handles[-1])
        print("6. Buka link maps di tab baru...")
        time.sleep(2)

        try:
            search_input = WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "input[name='q']"))
            )
            search_input.click()
            time.sleep(0.5)
            search_input.send_keys("\n")
            print("   Klik input search lalu Enter")
            time.sleep(3)
        except:
            print("   Input search tidak ditemukan, tetap pakai URL saat ini")
            time.sleep(2)

        current_url = driver.current_url
        driver.close()
        driver.switch_to.window(original_window)
        time.sleep(1)
        link_input = driver.find_element(By.NAME, "link_maps")
        link_input.clear()
        link_input.send_keys(current_url)
        driver.execute_script("arguments[0].dispatchEvent(new Event('input'));", link_input)
        print(f"   Link Maps: {current_url[:80]}...")

    if website_val:
        platform = get_platform(str(website_val))
        sosmed_select = driver.find_element(By.CSS_SELECTOR, "#addSosmedList select[name='sosmed_platform[]']")
        Select(sosmed_select).select_by_value(platform)
        sosmed_url = driver.find_element(By.CSS_SELECTOR, "#addSosmedList input[name='sosmed_url[]']")
        sosmed_url.send_keys(str(website_val))
        print(f"   Sosmed: {platform} -> {website_val}")

    simpan_btn = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, "#modalAdd button[type='submit']")))
    driver.execute_script("arguments[0].click();", simpan_btn)
    print("7. Simpan diklik")
    time.sleep(2)

print("\nSelesai semua data.")
time.sleep(5)