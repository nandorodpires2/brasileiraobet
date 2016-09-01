select	count(*)
from		aposta a;

select	a.aposta_vencedora_premio,
			count(*)
from		aposta a
where		a.aposta_vencedora = 1
group by a.aposta_vencedora_premio
;