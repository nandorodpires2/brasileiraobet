select	a.aposta_vencedora_premio,
			count(*) as apostas
from		aposta a 
where		a.aposta_vencedora = 1
group by a.aposta_vencedora_premio
			