import csv, json, requests
from glob import glob
from urllib.parse import quote_plus

ENDPOINT = "http://www.hicsuntleones.nl/erfgeoproxy/search/?contains=%s&type=hg:Building"

def request_data():
    with open("cinemas.csv") as f:
        reader = csv.DictReader(f)
        for row in reader:
            aid = row["address_id"]
            street = row["street_name"].split("-")[0]
            url = ENDPOINT % quote_plus("%s, Amsterdam" % street)
            print(url)
            req = requests.get(url)
            with open("data/%s.json" % aid, "w") as fw:
                fw.write(req.text)

def parse_json():
    out = open("cinemas-bag-rd.csv", "w")
    writer = csv.DictWriter(out, fieldnames=['address_id', 'bag_id', 'rm_id'])
    writer.writeheader()

    for path in glob("data/*.json"):
        aid = path.split(".")[0].replace("data/", "")
        with open(path) as f:
            data = json.loads(f.read())

            if len(data["results"]) < 1:
                continue

            result = data["results"][0]

            if len(result["bag"]) > 0:
                bagid = result["bag"]["id"].replace("bag/", "")

            if "rm" in result:
                try:
                    rmid = result["rm"]["id"].replace("rm/", "")
                except:
                    rmid = None
            else:
                rmid = None

            writer.writerow({
                "address_id" : aid,
                "bag_id" : bagid,
                "rm_id" : rmid
            })

if __name__ == "__main__":
    parse_json()
