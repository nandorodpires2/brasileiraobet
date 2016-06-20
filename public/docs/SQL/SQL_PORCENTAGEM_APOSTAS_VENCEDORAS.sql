/* PORCENTAGEM APOSTAS VENCEDORAS */
select	case a.aposta_vencedora
				when 1 then 'Sim'
				else 'Não'
			end as vencedora,
			count(*) as apostas			
from		aposta a
where		a.aposta_processada = 1
group by a.aposta_vencedora
