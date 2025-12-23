import serial
import time
import json
import sys
from datetime import datetime

# Update this to your specific COM port
SERIAL_PORT = 'COM6' 
BAUD_RATE = 115200

def get_inbox():
    try:
        ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=5)
        time.sleep(1)

        # 1. Set to Text Mode
        ser.write(b'AT+CMGF=1\r\n')
        time.sleep(0.5)

        # 2. List all messages
        ser.write(b'AT+CMGL="ALL"\r\n')
        time.sleep(1)
        
        response = ser.read_all().decode(errors='ignore')
        ser.close()

        messages = []
        lines = response.split('\n')
        
        for i in range(len(lines)):
            if "+CMGL:" in lines[i]:
                try:
                    meta = lines[i].split(',')
                    # Get Sender (Index 2)
                    sender = meta[2].replace('"', '').strip()
                    
                    # Get Date and Time (Index 4 and 5)
                    # Raw format usually: "25/12/22","10:30:00+32"
                    date_part = meta[4].replace('"', '').strip()
                    time_part = meta[5].replace('"', '').split('+')[0].strip() # Remove timezone offset
                    
                    # Convert to MySQL compatible format (YYYY-MM-DD HH:MM:SS)
                    # SIM800C gives YY/MM/DD
                    formatted_time = f"20{date_part.replace('/', '-')}- {time_part}"
                    
                    # The message body is usually on the very next line
                    content = lines[i+1].strip()

                    messages.append({
                        'sender': sender,
                        'time': formatted_time,
                        'message': content
                    })
                except (IndexError, ValueError) as e:
                    # Skip lines that don't match the expected SMS format
                    continue
        
        return json.dumps(messages)

    except Exception as e:
        return json.dumps([{"error": str(e)}])

if __name__ == "__main__":
    # Ensure the output is clean for Laravel's Process::run
    print(get_inbox())