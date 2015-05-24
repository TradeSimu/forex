import csv, sys, time
from datetime import datetime



csv_path = sys.argv[1]
period_seconds = [60, 300, 600, 1800, 3600, 10800]

prev_period = []
bid_ohlc = []
ask_ohlc = []

POS_O = 0
POS_H = 1
POS_L = 2
POS_C = 3

def format_record(prev_period, bid_ohlc, ask_ohlc):
	ds = datetime.fromtimestamp(prev_period).strftime('%Y-%m-%d %H:%M:%S')
	record = ','.join(
		[
			ds, 
			bid_ohlc[POS_O], bid_ohlc[POS_H], bid_ohlc[POS_L], bid_ohlc[POS_C], 
			ask_ohlc[POS_O], ask_ohlc[POS_H], ask_ohlc[POS_L], ask_ohlc[POS_C]
		]
	)
	return record

fps = []

for x in period_seconds:
	prev_period.append(None)
	bid_ohlc.append([])
	ask_ohlc.append([])
	fname = 'formatted/%d_%s' % (x, csv_path)
	fp = open(fname, 'w')
	fps.append(fp)

with open(csv_path, 'rb') as csvfile:
	reader = csv.reader(csvfile, delimiter=',')
	for row in reader:
		#print(row)
		date_obj = datetime.strptime(row[0], '%d/%m/%y %H:%M:%S')
		ts = int(date_obj.strftime('%s'))
		bid = row[1]
		ask = row[2]

		for i, p_size in enumerate(period_seconds):
			this_period = ts - ts % p_size
			if this_period == prev_period[i]:
				bid_ohlc[i][POS_C] = bid
				bid_ohlc[i][POS_H] = max(bid, bid_ohlc[i][POS_H])
				bid_ohlc[i][POS_L] = min(bid, bid_ohlc[i][POS_L])

				ask_ohlc[i][POS_C] = ask
				ask_ohlc[i][POS_H] = max(ask, ask_ohlc[i][POS_H])
				ask_ohlc[i][POS_L] = min(ask, ask_ohlc[i][POS_L])	
			else:
				if prev_period[i]:
					record = format_record(prev_period[i], bid_ohlc[i], ask_ohlc[i])
					#print(p_size)
					#print(record)
					fps[i].write(record)
					fps[i].write('\n')
				
				prev_period[i] = this_period

				# reset
				bid_ohlc[i] = [bid, bid, bid, bid]
				ask_ohlc[i] = [ask, ask, ask, ask]

# print the last record
for i, p_size in enumerate(period_seconds):
	record = format_record(prev_period[i], bid_ohlc[i], ask_ohlc[i])
	#print(p_size)
	#print(record)
	fps[i].write(record)
	fps[i].write('\n')

for fp in fps:
	fp.close()