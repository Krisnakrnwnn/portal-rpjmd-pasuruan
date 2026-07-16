import json
import sys

transcript_path = r"C:\Users\Krisnakrnwnn\.gemini\antigravity-ide\brain\00c1ce35-0206-4d90-a910-f1e26231ab43\.system_generated\logs\transcript.jsonl"

with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            data = json.loads(line)
            if 'tool_calls' in data:
                # Check for tool calls or tool responses
                pass
            if 'output' in data:
                # wait, responses are not in the main step block directly usually?
                pass
            
            # Since the transcript structure might have responses in 'content' or somewhere else, let's just dump lines containing 'Showing lines 460 to 620'
            if 'Showing lines 460 to 620' in line and 'dashboard.blade.php' in line:
                print("FOUND 460-620")
                with open("chunk1.txt", "w", encoding='utf-8') as out:
                    out.write(line)
            if 'Showing lines 620 to 650' in line and 'dashboard.blade.php' in line:
                print("FOUND 620-650")
                with open("chunk2.txt", "w", encoding='utf-8') as out:
                    out.write(line)
            if 'Showing lines 640 to 730' in line and 'dashboard.blade.php' in line:
                print("FOUND 640-730")
                with open("chunk3.txt", "w", encoding='utf-8') as out:
                    out.write(line)
        except Exception as e:
            pass
