import json

log_path = r'C:\Users\Manya Nirvan\.gemini\antigravity\brain\37b39d96-6fe9-43ee-abf9-eb6ec73c0425\.system_generated\logs\transcript.jsonl'

with open(log_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            data = json.loads(line)
            if 'content' in data and '"property-20"' in data['content']:
                print(f"Found in step {data.get('step_index')}")
                start = data['content'].find('"property-20"')
                print(data['content'][start:start+200])
        except Exception as e:
            pass
